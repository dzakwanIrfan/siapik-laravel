<?php

namespace App\Http\Controllers;

use App\Models\LetterType;
use App\Models\Submission;
use App\Models\LetterField;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Models\SubmissionValue;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\SubmissionStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class SubmissionController extends Controller
{
    // Halaman daftar surat (kartu + modal kosong)
    public function index()
    {
        $letter_types = LetterType::where('bitActive', 1)
            ->orderBy('txtNameLetterType')
            ->withCount(['letterFields' => fn ($q) => $q->where('bitActive', 1)])
            ->get();

        return view('pages.submissions.create.index', compact('letter_types'));
    }

    // Return potongan view fields untuk modal via AJAX
    public function form(int $letterTypeId)
    {
        $letterType = LetterType::with(['letterFields' => function ($q) {
            $q->where('bitActive', 1)->where('bitAkademik', 0)->orderBy('intFieldOrder');
        }])->findOrFail($letterTypeId);

        // Siapkan opsi select per field (keyed by field name)
        $fieldOptions = [];
        foreach ($letterType->letterFields as $f) {
            if ($f->txtFieldType === 'select') {
                $opts = [];
                $cfg  = $this->decodeJson($f->jsonFieldOptions);

                if (($cfg['source'] ?? 'static') === 'static') {
                    $opts = $cfg['options'] ?? [];
                } else {
                    $opts = $this->buildDynamicOptions($cfg);
                }

                $fieldOptions[$f->txtFieldName] = $opts;
            }
        }

        // Pakai view yang kamu lampirkan
        return view('pages.submissions.create.components._dynamic_fields', [
            'letterType'   => $letterType,
            'fieldOptions' => $fieldOptions,
        ]);
    }

    private function buildDynamicOptions(array $cfg): array
    {
        $model = $cfg['model'] ?? null;
        if (!$model) return [];

        // Asumsikan model di App\Models\
        $class = "\\App\\Models\\{$model}";
        if (!class_exists($class)) return [];

        $query = $class::query();

        // with: ["user", ...]
        $with = $cfg['with'] ?? [];
        if (is_array($with) && $with) {
            $query->with($with);
        }

        // Optional filter: { "where": { "bitActive": 1, "intMajor_ID": "@auth.dosenProfile.intMajor_ID" } }
        $where = $cfg['where'] ?? [];
        foreach ((array) $where as $col => $val) {
            $query->where($col, $this->resolveDynamicToken($val));
        }

        $rows  = $query->get();
        $label = $cfg['label'] ?? 'name';
        $value = $cfg['value'] ?? 'id';

        $out = [];
        foreach ($rows as $row) {
            $val = data_get($row, $value);
            $lab = data_get($row, $label);
            if ($val !== null && $lab !== null && $lab !== '') {
                // format sama seperti "options" static: [value => label]
                $out[(string) $val] = (string) $lab;
            }
        }

        // urutkan label supaya rapi
        asort($out, SORT_NATURAL | SORT_FLAG_CASE);
        return $out;
    }

    private function resolveDynamicToken($val)
    {
        // support token seperti "@auth.dosenProfile.intMajor_ID"
        if (is_string($val) && str_starts_with($val, '@auth.')) {
            $path = substr($val, 6); // hapus "@auth."
            return data_get(auth()->user(), $path);
        }
        return $val;
    }


    // Simpan pengajuan
    public function store(Request $request)
    {
        $request->validate([
            'letter_type_id' => 'required|integer|exists:letter_types,intLetterType_ID',
        ]);

        $letterType = LetterType::with(['letterFields' => function ($q) {
            $q->where('bitActive', 1)->where('bitAkademik', 0)->orderBy('intFieldOrder');
        }])->findOrFail($request->letter_type_id);

        // Bangun rules validasi berdasar definisi field
        [$rules, $selectInMap] = $this->buildValidationRules($letterType->letterFields);
        $validated = $request->validate($rules); // menghasilkan fields[xxx]

        DB::beginTransaction();
        try {
            // 1) Create submission
            $submission = Submission::create([
                'intLetterType_ID' => $letterType->intLetterType_ID,
                'intUser_ID'       => auth()->id(),
                'txtReceiptNumber' => $this->generateSubmissionNumber(),
                'bitActive'        => 1,
                'txtInsertedBy'    => optional(auth()->user())->name ?? 'System',
                'dtmInserted'      => now(),
                'txtUpdatedBy'     => optional(auth()->user())->name ?? 'System',
                'dtmUpdated'       => now(),
            ]);

            // 2) Simpan tiap nilai field
            $fieldInputs = $request->input('fields', []);
            foreach ($letterType->letterFields as $field) {
                $name   = $field->txtFieldName;
                $label  = $field->txtFieldLabel;
                $type   = $field->txtFieldType;
                $value  = null;
                $meta   = null;

                if ($type === 'file') {
                    if ($request->hasFile("fields.$name")) {
                        $file = $request->file("fields.$name");

                        // simpan di storage/app/public/submissions/{id}/
                        $dir  = "submissions/{$submission->intSubmission_ID}";
                        $path = $file->store($dir, ['disk' => 'public']);

                        $value = $path; // simpan path relatif
                        $meta  = [
                            'original_name' => $file->getClientOriginalName(),
                            'mime'          => $file->getClientMimeType(),
                            'size'          => $file->getSize(),
                            'disk'          => 'public',
                            'url'           => Storage::disk('public')->url($path),
                        ];
                    }
                } else {
                    // text/textarea/number/date/select
                    $value = Arr::get($fieldInputs, $name);
                }

                SubmissionValue::create([
                    'intSubmission_ID'  => $submission->intSubmission_ID,
                    'intLetterField_ID' => $field->intLetterField_ID,
                    'txtFieldName'      => $name,
                    'txtFieldLabel'     => $label,
                    'txtFieldType'      => $type,
                    'txtFieldValue'     => is_array($value) ? json_encode($value) : $value,
                    'jsonFieldMeta'     => $meta,
                    'bitActive'         => 1,
                    'txtInsertedBy'     => optional(auth()->user())->name ?? 'System',
                    'dtmInserted'       => now(),
                    'txtUpdatedBy'      => optional(auth()->user())->name ?? 'System',
                    'dtmUpdated'        => now(),
                ]);
            }

            SubmissionStatus::create([
                'intSubmission_ID' => $submission->intSubmission_ID,
                'txtStatus'        => 'Sedang ditinjau Kaprodi',
                'txtInReview'      => 'Persetujuan Kaprodi',
                'bitActive'       => 1,
                'txtInsertedBy'   => auth()->user()->txtFullName ?? 'System',
                'dtmInserted'     => now(),
            ]);

            DB::commit();

            return redirect()
                ->route('submissions.receipt', $submission->intSubmission_ID)
                ->with('success', 'Pengajuan berhasil dibuat.');
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            dd($e);
            return back()->with('error', 'Gagal menyimpan pengajuan.')->withInput();
        }
    }

    // Halaman receipt
    public function receipt(int $submissionId)
    {
        $submission = Submission::with(['letterType', 'values', 'user'])
            ->findOrFail($submissionId);
        return view('pages.submissions.receipt.receipt', compact('submission'));
    }

    // Download receipt as PDF
    public function downloadReceipt(int $submissionId)
    {
        $submission = Submission::with(['letterType', 'values', 'user'])
            ->findOrFail($submissionId);

        $pdf = Pdf::loadView('pages.submissions.receipt.receipt_pdf', compact('submission'));
        $pdf->setPaper('A4', 'portrait');

        $filename = 'Receipt_' . $submission->letterType->txtCode . '_' . date('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }

    // Print receipt (view optimized for printing)
    public function printReceipt(int $submissionId)
    {
        $submission = Submission::with(['letterType', 'values', 'user'])
            ->findOrFail($submissionId);
        return view('pages.submissions.receipt.receipt_print', compact('submission'));
    }

    private function buildValidationRules($fields): array
    {
        $rules = ['fields' => 'required|array'];
        $selectIn = []; // map name => allowed values (untuk select)

        foreach ($fields as $f) {
            $name   = $f->txtFieldName;
            $type   = $f->txtFieldType;
            $extra  = $this->decodeJson($f->jsonFieldValidation);
            $req    = (int)$f->bitRequired === 1;

            $r = [];
            $r[] = $req ? 'required' : 'nullable';

            switch ($type) {
                case 'text':
                case 'textarea':
                    $r[] = 'string';
                    if (isset($extra['maxlength'])) $r[] = 'max:'.$extra['maxlength'];
                    if (isset($extra['minlength'])) $r[] = 'min:'.$extra['minlength'];
                    if (isset($extra['pattern']))   $r[] = 'regex:'.$extra['pattern'];
                    break;

                case 'email':
                    $r[] = 'email';
                    break;

                case 'number':
                    $r[] = 'numeric';
                    if (isset($extra['min'])) $r[] = 'min:'.$extra['min'];
                    if (isset($extra['max'])) $r[] = 'max:'.$extra['max'];
                    break;

                case 'date':
                    $r[] = 'date';
                    break;

                case 'select':
                    $r[] = 'string';
                    $opts = $this->decodeJson($f->jsonFieldOptions);
                    if (($opts['source'] ?? 'static') === 'static') {
                        $allowed = array_keys($opts['options'] ?? []);
                        // handle array numerik [a,b] => value=label
                        if ($allowed === array_values($allowed)) {
                            $allowed = $opts['options'] ?? [];
                        }
                        if (!empty($allowed)) {
                            $selectIn[$name] = $allowed;
                            $r[] = 'in:'.implode(',', array_map(fn($v) => str_replace(',', '\,', $v), $allowed));
                        }
                    }
                    break;

                case 'file':
                    $r[] = 'file';
                    if (!empty($extra['mimes']))     $r[] = 'mimes:'.implode(',', (array)$extra['mimes']);
                    if (!empty($extra['mimetypes'])) $r[] = 'mimetypes:'.implode(',', (array)$extra['mimetypes']);
                    if (!empty($extra['max']))       $r[] = 'max:'.$extra['max']; // KB
                    break;

                default:
                    $r[] = 'nullable';
            }

            $rules["fields.$name"] = implode('|', $r);
        }

        return [$rules, $selectIn];
    }

    private function decodeJson($val): array
    {
        if (is_array($val)) return $val;
        if (is_string($val) && strlen($val)) {
            $d = json_decode($val, true);
            return is_array($d) ? $d : [];
        }
        return [];
    }

    private function generateSubmissionNumber(): string
    {
        $year  = date('Y');
        $month = date('m');

        $count = DB::table('submissions')
            ->whereYear('dtmInserted', $year)
            ->whereMonth('dtmInserted', $month)
            ->count() + 1;

        return sprintf('RCP/%s/%s/%04d', $year, $month, $count);
    }

    public function mySubmissions(Request $request)
    {
        return view('pages.submissions.history.index');
    }

    public function mySubmissionsDatatable()
    {
        $query = Submission::join('letter_types', 'submissions.intLetterType_ID', '=', 'letter_types.intLetterType_ID')
            ->where('submissions.bitActive', 1)
            ->where('intUser_ID', auth()->id())
            ->select('submissions.*', 'letter_types.txtNameLetterType');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('letter_type', fn($row) => $row->letterType->txtNameLetterType ?? '-')
            ->addColumn('dtmInserted', fn($row) => $row->dtmInserted ?? '-')
            ->addColumn('action', function ($r) {
                $btnGroup = '<div class="btn-group" role="group">';

                // Tombol Chat
                $btnGroup .= '<button type="button" class="btn btn-primary btn-action chat-btn" data-id="' . $r->intSubmission_ID . '"><i class="fas fa-comments"></i></button>';

                // Tombol Tanda Terima
                $btnGroup .= '<a href="'.route('submissions.receipt', $r->intSubmission_ID).'" class="btn btn-success btn-action" title="Lihat Tanda Terima" target="_blank"><i class="fas fa-receipt"></i></a>';

                // Tombol Revisi (EDIT) hanya muncul jika status ditolak
                if (in_array($r->txtStatus, ['Ditolak Kaprodi', 'Ditolak Akademik'])) {
                    $btnGroup .= '<button type="button" class="btn btn-warning btn-action revise-btn" data-id="' . $r->intSubmission_ID . '" title="Revisi Pengajuan"><i class="fas fa-edit"></i></button>';
                }

                $btnGroup .= '</div>';
                return $btnGroup;
            })
            ->addColumn('status', function ($r) {
                return $this->getStatusButton($r);
            })
            ->filterColumn('letter_type', function($query, $keyword) {
                $query->whereHas('letterType', function($q) use ($keyword) {
                    $q->where('txtNameLetterType', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn('dtmInserted', function($query, $keyword) { // Perbarui ke dtmInserted
                $query->where('dtmInserted', 'like', "%{$keyword}%");
            })
            ->rawColumns(['action', 'status'])
            ->make(true);
    }

    public function submissionStatusesDatatable(Submission $submission)
    {
        $query = SubmissionStatus::query()
            ->where('intSubmission_ID', $submission->intSubmission_ID)
            ->orderByDesc('dtmInserted');

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('bitActive', function ($r) {
                return $r->bitActive
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-secondary">Inactive</span>';
            })
            ->rawColumns(['bitActive'])
            ->make(true);
    }

    public function chatIndex(Submission $submission)
    {
        $submission->load('user');
        $chats = $submission->chats()->with('user.roles')->orderBy('created_at', 'asc')->get();

        // Log data ke file tanpa menghentikan eksekusi
        Log::info($chats);

        return view('pages.submissions.chat.content', compact('submission', 'chats'));
    }

    public function chatStore(Request $request, Submission $submission)
    {
        $request->validate([
            'txtMessage' => 'required|string|max:2000' // max:2000 adalah contoh, bisa disesuaikan
        ]);


        $submission->chats()->create([
            'intUser_ID' => auth()->id(), // Ambil ID user yang sedang login
            'txtMessage' => $request->txtMessage
        ]);

        return response()->json(['success' => 'Pesan terkirim!']);
    }

    public function editFormModal(Submission $submission)
    {
        if ($submission->intUser_ID !== auth()->id()) {
            abort(403);
        }
        if (!in_array($submission->txtStatus, ['Ditolak Kaprodi', 'Ditolak Akademik'])) {
            abort(403, 'Pengajuan ini tidak dapat direvisi.');
        }

        $letterType = $submission->letterType()->with(['letterFields' => function ($q) {
            $q->where('bitActive', 1)->where('bitAkademik', 0)->orderBy('intFieldOrder');
        }])->first();

        // Ambil nilai-nilai yang sudah ada
        $currentValues = $submission->values->pluck('txtFieldValue', 'txtFieldName')->all();

        $fieldOptions = [];
        foreach ($letterType->letterFields as $f) {
            if ($f->txtFieldType === 'select') {
                $opts = [];
                $cfg  = $this->decodeJson($f->jsonFieldOptions);
                if (($cfg['source'] ?? 'static') === 'static') {
                    $opts = $cfg['options'] ?? [];
                } else {
                    $opts = $this->buildDynamicOptions($cfg);
                }
                $fieldOptions[$f->txtFieldName] = $opts;
            }
        }

        // Return view partial yang berisi komponen dinamis
        return view('pages.submissions.history.components._revise_form_modal_content', [
            'submission'    => $submission,
            'letterType'    => $letterType,
            'currentValues' => $currentValues,
            'fieldOptions'  => $fieldOptions,
        ]);
    }


    public function update(Request $request, Submission $submission)
    {
        // Pastikan hanya pemilik yang bisa mengedit
        if ($submission->intUser_ID !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Pastikan hanya status ditolak yang bisa diedit
        if (!in_array($submission->txtStatus, ['Ditolak Kaprodi', 'Ditolak Akademik'])) {
            return response()->json(['error' => 'Pengajuan ini tidak dapat direvisi.'], 400);
        }

        $letterType = $submission->letterType()->with(['letterFields' => function ($q) {
            $q->where('bitActive', 1)->where('bitAkademik', 0)->orderBy('intFieldOrder');
        }])->first();

        // Bangun rules validasi berdasar definisi field
        [$rules, $selectInMap] = $this->buildValidationRules($letterType->letterFields);

        // Tambahkan validasi khusus untuk file: jika ada file baru, wajib divalidasi
        foreach ($letterType->letterFields as $field) {
            if ($field->txtFieldType === 'file' && $request->hasFile("fields.{$field->txtFieldName}")) {
                $rules["fields.{$field->txtFieldName}"] = str_replace('nullable', 'required', $rules["fields.{$field->txtFieldName}"]);
            }
        }

        $validated = $request->validate($rules);

        DB::beginTransaction();
        try {
            // Hapus nilai submission_values lama untuk field-field yang direvisi
            $submission->values()->whereIn('intLetterField_ID', $letterType->letterFields->pluck('intLetterField_ID'))->delete();

            // Simpan tiap nilai field yang baru
            $fieldInputs = $request->input('fields', []);
            foreach ($letterType->letterFields as $field) {
                $name   = $field->txtFieldName;
                $label  = $field->txtFieldLabel;
                $type   = $field->txtFieldType;
                $value  = null;
                $meta   = null;

                if ($type === 'file') {
                    if ($request->hasFile("fields.$name")) {
                        $file = $request->file("fields.$name");

                        // Hapus file lama jika ada (opsional, tergantung kebijakan Anda)
                        $oldFileValue = $submission->values()->where('txtFieldName', $name)->first();
                        if ($oldFileValue && Storage::disk('public')->exists($oldFileValue->txtFieldValue)) {
                            Storage::disk('public')->delete($oldFileValue->txtFieldValue);
                        }

                        $dir  = "submissions/{$submission->intSubmission_ID}";
                        $path = $file->store($dir, ['disk' => 'public']);

                        $value = $path;
                        $meta  = [
                            'original_name' => $file->getClientOriginalName(),
                            'mime'          => $file->getClientMimeType(),
                            'size'          => $file->getSize(),
                            'disk'          => 'public',
                            'url'           => Storage::disk('public')->url($path),
                        ];
                    } else {
                        // Jika tidak ada file baru di-upload, dan sebelumnya ada file, pertahankan nilai lama
                        $oldFileValue = $submission->values()->where('txtFieldName', $name)->first();
                        if ($oldFileValue) {
                            $value = $oldFileValue->txtFieldValue;
                            $meta = $oldFileValue->jsonFieldMeta;
                        }
                    }
                } else {
                    $value = Arr::get($fieldInputs, $name);
                }

                SubmissionValue::create([
                    'intSubmission_ID'  => $submission->intSubmission_ID,
                    'intLetterField_ID' => $field->intLetterField_ID,
                    'txtFieldName'      => $name,
                    'txtFieldLabel'     => $label,
                    'txtFieldType'      => $type,
                    'txtFieldValue'     => is_array($value) ? json_encode($value) : $value,
                    'jsonFieldMeta'     => $meta,
                    'bitActive'         => 1,
                    'txtInsertedBy'     => auth()->user()->txtFullName ?? 'System',
                    'dtmInserted'       => now(),
                    'txtUpdatedBy'      => auth()->user()->txtFullName ?? 'System',
                    'dtmUpdated'        => now(),
                ]);
            }

            // Update status kembali ke 'Sedang ditinjau Kaprodi' dan buat entri status baru
            $submission->update([
                'txtStatus'     => 'Sedang ditinjau Kaprodi',
                'txtUpdatedBy'  => auth()->user()->txtFullName ?? 'System',
                'dtmUpdated'    => now(),
            ]);

            // Nonaktifkan status lama yang aktif
            SubmissionStatus::where('intSubmission_ID', $submission->intSubmission_ID)
                            ->where('bitActive', 1)
                            ->update(['bitActive' => 0]);

            // Buat status baru
            SubmissionStatus::create([
                'intSubmission_ID' => $submission->intSubmission_ID,
                'txtStatus'        => 'Sedang ditinjau Kaprodi',
                'txtInReview'      => 'Persetujuan Kaprodi',
                'txtNotes'         => 'Revisi diajukan ulang oleh pemohon.',
                'bitActive'        => 1,
                'txtInsertedBy'    => auth()->user()->txtFullName ?? 'System',
                'dtmInserted'      => now(),
            ]);

            DB::commit();

            return response()->json(['success' => 'Revisi berhasil disimpan dan pengajuan dikirim ulang.']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("Error revising submission: " . $e->getMessage(), ['submission_id' => $submission->intSubmission_ID, 'exception' => $e]);
            return response()->json(['error' => 'Gagal menyimpan revisi. ' . $e->getMessage()], 500);
        }
    }

    private function getStatusButton($r)
    {
        // Sedikit perbaikan: gunakan relasi langsung untuk keamanan
        $letterTypeName = optional($r->letterType)->txtNameLetterType ?? 'Surat';

        switch ($r->txtStatus ?? null) {
            case 'Sedang ditinjau Kaprodi':
                return '<button class="btn btn-sm btn-primary rounded-pill show-status-modal"
                            data-bs-toggle="modal" data-bs-target="#submissionModal"
                            data-submissions-id="'.$r->intSubmission_ID.'" data-type-name="'.$letterTypeName.'">
                            Sedang ditinjau Kaprodi</button>';
            case 'Disetujui Kaprodi':
                return '<button class="btn btn-sm btn-success rounded-pill show-status-modal"
                            data-bs-toggle="modal" data-bs-target="#submissionModal"
                            data-submissions-id="'.$r->intSubmission_ID.'" data-type-name="'.$letterTypeName.'">
                            Disetujui Kaprodi</button>';
            case 'Ditolak Kaprodi':
                return '<button class="btn btn-sm btn-danger rounded-pill show-status-modal"
                            data-bs-toggle="modal" data-bs-target="#submissionModal"
                            data-submissions-id="'.$r->intSubmission_ID.'" data-type-name="'.$letterTypeName.'">
                            Ditolak Kaprodi</button>';
            case 'Disetujui Akademik':
                return '<button class="btn btn-sm btn-info rounded-pill show-status-modal"
                            data-bs-toggle="modal" data-bs-target="#submissionModal"
                            data-submissions-id="'.$r->intSubmission_ID.'" data-type-name="'.$letterTypeName.'">
                            Disetujui Akademik</button>';
            case 'Ditolak Akademik':
                return '<button class="btn btn-sm btn-warning rounded-pill show-status-modal"
                            data-bs-toggle="modal" data-bs-target="#submissionModal"
                            data-submissions-id="'.$r->intSubmission_ID.'" data-type-name="'.$letterTypeName.'">
                            Ditolak Akademik</button>';
            case 'Sudah dicetak':
                return '<button class="btn btn-sm btn-secondary rounded-pill show-status-modal"
                            data-bs-toggle="modal" data-bs-target="#submissionModal"
                            data-submissions-id="'.$r->intSubmission_ID.'" data-type-name="'.$letterTypeName.'">
                            Sudah dicetak</button>';
            default:
                return e($r->txtStatus ?? '-');
        }
    }
}

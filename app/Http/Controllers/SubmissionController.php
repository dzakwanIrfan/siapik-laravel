<?php

namespace App\Http\Controllers;

use App\Models\LetterType;
use App\Models\Submission;
use App\Models\LetterField;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Models\SubmissionValue;
use Illuminate\Support\Facades\DB;
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
            $q->where('bitActive', 1)->orderBy('intFieldOrder');
        }])->findOrFail($letterTypeId);

        // Siapkan opsi select per field (keyed by field name)
        $fieldOptions = [];
        foreach ($letterType->letterFields as $f) {
            if ($f->txtFieldType === 'select') {
                $opts = [];
                $cfg  = $this->decodeJson($f->jsonFieldOptions);
                if (($cfg['source'] ?? 'static') === 'static') {
                    $opts = $cfg['options'] ?? [];
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

    // Simpan pengajuan
    public function store(Request $request)
    {
        $request->validate([
            'letter_type_id' => 'required|integer|exists:letter_types,intLetterType_ID',
        ]);

        $letterType = LetterType::with(['letterFields' => function ($q) {
            $q->where('bitActive', 1)->orderBy('intFieldOrder');
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

            DB::commit();

            return redirect()
                ->route('submissions.receipt', $submission->intSubmission_ID)
                ->with('success', 'Pengajuan berhasil dibuat.');
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
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

    // Unduh PDF (opsional): butuh barryvdh/laravel-dompdf
    public function download(int $submissionId)
    {
        $submission = Submission::with(['letterType', 'values'])
            ->findOrFail($submissionId);

        if (! class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            // fallback: kembalikan HTML untuk dicetak (tanpa custom CSS)
            return $this->receipt($submissionId);
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pages.submissions.receipt-pdf', [
            'submission' => $submission,
        ])->setPaper('A4');

        $filename = 'Receipt_'.$submission->txtReceiptNumber.'.pdf';
        return $pdf->download($filename);
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
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
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
                ->addColumn('dtmCreated', fn($row) => $row->dtmCreated ?? '-')
                ->addColumn('action', function ($r) {
                    return '<div class="btn-group" role="group">
                                <button type="button" class="btn btn-info btn-action btn-view"><i class="fas fa-eye"></i></button>
                                <button type="button" class="btn btn-warning btn-action btn-edit"><i class="fas fa-edit"></i></button>
                                <button type="button" class="btn btn-danger btn-action btn-delete"><i class="fas fa-trash-alt"></i></button>
                            </div>';
                })
                ->filterColumn('letter_type', function($query, $keyword) {
                    $query->whereHas('letterType', function($q) use ($keyword) {
                        $q->where('txtNameLetterType', 'like', "%{$keyword}%");
                    });
                })
                ->filterColumn('dtmCreated', function($query, $keyword) {
                    $query->where('dtmCreated', 'like', "%{$keyword}%");
                })
                ->make(true);
    }
}

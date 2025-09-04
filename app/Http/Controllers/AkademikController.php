<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Models\SubmissionValue;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\SubmissionStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class AkademikController extends Controller
{
    public function index($type, $status)
    {
        // Validasi parameter
        if (!in_array($type, ['surat', 'ujian']) || !in_array($status, ['proses', 'selesai'])) {
            abort(404);
        }

        // Tentukan status berdasarkan parameter
        $statusConditions = $this->getStatusConditions($type, $status);

        // Hitung jumlah submission berdasarkan kondisi
        $count = Submission::where('submissions.bitActive', 1)
            ->whereIn('submissions.txtStatus', $statusConditions)
            ->when($type === 'ujian', function($q) {
                $q->join('letter_types', 'submissions.intLetterType_ID', '=', 'letter_types.intLetterType_ID')
                  ->where('letter_types.bitUjian', 1);
            })
            ->when($type === 'surat', function($q) {
                $q->join('letter_types', 'submissions.intLetterType_ID', '=', 'letter_types.intLetterType_ID')
                  ->where('letter_types.bitUjian', 0);
            })
            ->count();

        // Data untuk view
        $pageData = [
            'type' => $type,
            'status' => $status,
            'count' => $count,
            'pageTitle' => $this->getPageTitle($type, $status),
            'pageDescription' => $this->getPageDescription($type, $status),
            'alertMessage' => $this->getAlertMessage($type, $status, $count)
        ];

        return view('pages.submissions.akademik.index', $pageData);
    }

    public function indexDatatable($type, $status)
    {
        // Validasi parameter
        if (!in_array($type, ['surat', 'ujian']) || !in_array($status, ['proses', 'selesai'])) {
            return response()->json(['error' => 'Invalid parameters'], 400);
        }

        $majorId = optional(auth()->user()->dosenProfile)->intMajor_ID;
        $statusConditions = $this->getStatusConditions($type, $status);

        $query = Submission::query()
            ->join('letter_types', 'submissions.intLetterType_ID', '=', 'letter_types.intLetterType_ID')
            ->join('users', 'submissions.intUser_ID', '=', 'users.intUser_ID')
            ->join('mahasiswa_profiles', 'users.intUser_ID', '=', 'mahasiswa_profiles.intUser_ID')
            ->when($majorId, fn ($q) => $q->where('mahasiswa_profiles.intMajor_ID', $majorId))
            ->where('submissions.bitActive', 1)
            ->whereIn('submissions.txtStatus', $statusConditions)
            ->when($type === 'ujian', fn($q) => $q->where('letter_types.bitUjian', 1))
            ->when($type === 'surat', fn($q) => $q->where('letter_types.bitUjian', 0))
            ->select([
                'submissions.*',
                'letter_types.txtNameLetterType as letter_type',
                'users.txtFullName as user_full_name',
            ]);

        return \DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('letter_type', fn($row) => $row->letter_type ?? '-')
            ->editColumn('dtmInserted', fn($row) => $row->dtmInserted ?? '-')
            ->addColumn('action', function ($r) use ($status) {
                return $this->getActionButtons($r, $status);
            })
            ->addColumn('status', function ($r) {
                return $this->getStatusButton($r);
            })
            ->addColumn('user_full_name', fn($row) => $row->user_full_name ?? '-')
            ->filterColumn('letter_type', function($query, $keyword) {
                $query->where('letter_types.txtNameLetterType', 'like', "%{$keyword}%");
            })
            ->filterColumn('user_full_name', function($query, $keyword) {
                $query->where('users.txtFullName', 'like', "%{$keyword}%");
            })
            ->filterColumn('dtmInserted', function($query, $keyword) {
                $query->where('submissions.dtmInserted', 'like', "%{$keyword}%");
            })
            ->rawColumns(['action', 'status'])
            ->make(true);
    }

    private function getStatusConditions($type, $status)
    {
        if ($status === 'proses') {
            return ['Disetujui Kaprodi', 'Disetujui Akademik', 'Sudah dicetak'];
        } else { // selesai
            return ['Ditolak Kaprodi', 'Ditolak Akademik'];
        }
    }

    private function getPageTitle($type, $status)
    {
        $typeText = $type === 'surat' ? 'Surat' : 'Ujian';
        $statusText = $status === 'proses' ? 'Dalam Proses' : 'Selesai/Ditolak';
        return "Permintaan {$typeText} - {$statusText}";
    }

    private function getPageDescription($type, $status)
    {
        $typeText = $type === 'surat' ? 'surat' : 'ujian';
        $statusText = $status === 'proses' ? 'yang sedang dalam proses' : 'yang sudah selesai atau ditolak';
        return "Sistem Informasi Pembuatan {$typeText} {$statusText}";
    }

    private function getAlertMessage($type, $status, $count)
    {
        if ($status === 'proses') {
            $typeText = $type === 'surat' ? 'surat' : 'ujian';
            return "{$count} permintaan {$typeText} belum selesai diproses!";
        } else {
            $typeText = $type === 'surat' ? 'surat' : 'ujian';
            return "{$count} permintaan {$typeText} sudah selesai diproses atau ditolak.";
        }
    }

    private function getActionButtons($r, $status)
    {
        $buttons = '<div class="d-flex gap-1" role="group">';

        // Tombol Chat
        $buttons .= '<button type="button" class="btn btn-primary btn-sm rounded-pill icon chat-btn"
                    data-bs-toggle="tooltip" data-bs-placement="top" title="Diskusi Surat"
                    data-id="'.$r->intSubmission_ID.'">
                    <i class="fas fa-comments"></i>
                </button>';

        // Tombol Cek Lampiran
        $buttons .= '<button type="button" class="btn btn-info btn-sm rounded-pill icon show-attachment-modal"
                        data-bs-toggle="tooltip" data-bs-placement="top" title="Cek bukti lampiran"
                        data-submission-id="'.$r->intSubmission_ID.'">
                        <i class="fas fa-eye"></i>
                    </button>';

        // Tombol Edit hanya untuk status proses
        if ($status === 'proses') {
            $buttons .= '<button type="button" class="btn btn-secondary btn-sm rounded-pill icon btn-open-letter"
                            data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Inputan Mahasiswa"
                            data-submission-id="'.$r->intSubmission_ID.'">
                            <i class="fas fa-edit"></i>
                        </button>';
        }

        // Tombol berdasar status
        if (in_array($r->txtStatus, ['Disetujui Akademik', 'Sudah dicetak', 'Disetujui Kaprodi'])) {
            $buttons .= '<a href="'.route('akademik.submissions.preview', $r->intSubmission_ID).'"
                            class="btn btn-primary btn-sm rounded-pill icon"
                            data-bs-toggle="tooltip" data-bs-placement="top" title="Proses Surat" target="_blank">
                            <i class="fas fa-cog"></i>
                        </a>';
        }

        $buttons .= '</div>';

        return $buttons;
    }

    private function getStatusButton($r)
    {
        switch ($r->txtStatus ?? null) {
            case 'Sedang ditinjau Kaprodi':
                return '<button class="btn btn-sm btn-primary rounded-pill show-status-modal"
                            data-bs-toggle="modal" data-bs-target="#submissionModal"
                            data-submissions-id="'.$r->intSubmission_ID.'" data-type-name="'.$r->letter_type.'">
                            Sedang ditinjau Kaprodi</button>';
            case 'Disetujui Kaprodi':
                return '<button class="btn btn-sm btn-info rounded-pill show-status-modal"
                            data-bs-toggle="modal" data-bs-target="#submissionModal"
                            data-submissions-id="'.$r->intSubmission_ID.'" data-type-name="'.$r->letter_type.'">
                            Disetujui Kaprodi</button>';
            case 'Disetujui Akademik':
                return '<button class="btn btn-sm btn-success rounded-pill show-status-modal"
                            data-bs-toggle="modal" data-bs-target="#submissionModal"
                            data-submissions-id="'.$r->intSubmission_ID.'" data-type-name="'.$r->letter_type.'">
                            Disetujui Akademik</button>';
            case 'Sudah dicetak':
                return '<button class="btn btn-sm btn-warning rounded-pill show-status-modal"
                            data-bs-toggle="modal" data-bs-target="#submissionModal"
                            data-submissions-id="'.$r->intSubmission_ID.'" data-type-name="'.$r->letter_type.'">
                            Sudah dicetak</button>';
            case 'Ditolak Kaprodi':
            case 'Ditolak Akademik':
                return '<button class="btn btn-sm btn-danger rounded-pill show-status-modal"
                            data-bs-toggle="modal" data-bs-target="#submissionModal"
                            data-submissions-id="'.$r->intSubmission_ID.'" data-type-name="'.$r->letter_type.'">
                            '.$r->txtStatus.'</button>';
            default:
                return e($r->txtStatus ?? '-');
        }
    }

    public function getAttachments($submissionId)
    {
        try {
            $submission = Submission::with(['letterType', 'user', 'values.letterField'])
                ->findOrFail($submissionId);

            // Filter hanya field yang bertipe file
            $attachments = $submission->values()
                ->whereHas('letterField', function($query) {
                    $query->where('txtFieldType', 'file');
                })
                ->with('letterField')
                ->get();

            $attachmentData = [];
            foreach ($attachments as $attachment) {
                if ($attachment->txtFieldValue && file_exists(storage_path('app/public/' . $attachment->txtFieldValue))) {
                    $filePath = $attachment->txtFieldValue;
                    $fileName = basename($filePath);
                    $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
                    $fileUrl = asset('storage/' . $filePath);

                    $attachmentData[] = [
                        'field_label' => $attachment->letterField->txtFieldLabel ?? $attachment->txtFieldLabel,
                        'field_name' => $attachment->letterField->txtFieldName ?? $attachment->txtFieldName,
                        'file_name' => $fileName,
                        'file_url' => $fileUrl,
                        'file_extension' => strtolower($fileExtension),
                        'is_image' => in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif', 'webp']),
                        'is_pdf' => strtolower($fileExtension) === 'pdf'
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'submission' => [
                        'id' => $submission->intSubmission_ID,
                        'receipt_number' => $submission->txtReceiptNumber,
                        'letter_type' => $submission->letterType->txtNameLetterType ?? '',
                        'user_name' => $submission->user->txtFullName ?? '',
                        'status' => $submission->txtStatus
                    ],
                    'attachments' => $attachmentData
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data lampiran: ' . $e->getMessage()
            ], 500);
        }
    }

    public function previewSubmission($submissionId)
    {
        try {
            $submission = Submission::with(['letterType', 'user.mahasiswaProfile.major', 'values.letterField'])
                ->whereIn('txtStatus', ['Disetujui Kaprodi', 'Disetujui Akademik', 'Ditolak Akademik', 'Sudah dicetak'])
                ->findOrFail($submissionId);

            // Kumpulkan semua data dari submission values
            $submissionData = [];
            foreach ($submission->values as $value) {
                $fieldName = $value->letterField->txtFieldName ?? $value->txtFieldName;
                $submissionData[$fieldName] = $value->txtFieldValue;
            }

            $attachments = $submission->values()->whereHas('letterField', function($q) {
                                $q->where('txtFieldType', 'file');
                            })->with('letterField')->get();

            // Tentukan type berdasarkan bitUjian
            $type = $submission->letterType->bitUjian == 1 ? 'ujian' : 'surat';

            // Tentukan status berdasarkan txtStatus submission
            $status = in_array($submission->txtStatus, ['Disetujui Kaprodi', 'Disetujui Akademik', 'Ditolak Akademik', 'Sudah dicetak']) ? 'proses' : 'selesai';

            return view('pages.submissions.akademik.preview', [
                'submission' => $submission,
                'data' => $submissionData,
                'attachments' => $attachments,
                'type' => $type,
                'status' => $status
            ]);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal memuat preview surat: ' . $e->getMessage());
        }
    }

    public function getLetterPreviewHtml($submissionId)
    {
        try {
            $submission = Submission::with(['letterType', 'user.mahasiswaProfile.major', 'values.letterField'])
                ->whereIn('txtStatus', ['Disetujui Kaprodi', 'Disetujui Akademik', 'Ditolak Akademik', 'Sudah dicetak'])
                ->findOrFail($submissionId);

            // Kumpulkan semua data dari submission values
            $submissionData = [];
            foreach ($submission->values as $value) {
                $fieldName = $value->letterField->txtFieldName ?? $value->txtFieldName;
                $submissionData[$fieldName] = $value->txtFieldValue;
            }

            $templatePath = $submission->letterType->txtTemplatePath ?? 'templates.default_letter';

            if (View::exists($templatePath)) {
                return view($templatePath, ['data' => $submissionData, 'submission' => $submission]);
            } else {
                return view('templates.default_letter', ['data' => $submissionData, 'submission' => $submission]);
            }

        } catch (\Exception $e) {
            return response('<div style="padding: 20px; text-align: center; color: red; font-family: Arial;">Error: ' . $e->getMessage() . '</div>');
        }
    }

    public function processSubmission(Request $request, $submissionId)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'txtLetterNumber' => 'required_if:action,approve|nullable|string|max:255',
            'txtCatatan' => 'required_if:action,reject|nullable|string|max:1000',
        ]);

        try {
            $submission_statuses = SubmissionStatus::where('intSubmission_ID', $submissionId)->where('bitActive', 1)->first();
            DB::beginTransaction();

            $submission = Submission::findOrFail($submissionId);
            $oldStatus = $submission->txtStatus;

            $newStatus = $request->action === 'approve' ? 'Disetujui Akademik' : 'Ditolak Akademik';

            $submission->update([
                'txtStatus' => $newStatus,
                'txtLetterNumber' => $request->txtLetterNumber,
                'txtUpdatedBy' => auth()->user()->txtFullName,
                'dtmUpdated' => now()
            ]);

            if ($oldStatus !== $newStatus) {
                $submission_statuses->update([
                    'bitActive' => 0,
                    'txtUpdatedBy' => auth()->user()->txtFullName,
                    'dtmUpdated' => now()
                ]);

                $txtInReview = $newStatus === 'Disetujui Akademik' ? 'Menunggu dicetak' : 'Ditolak';

                SubmissionStatus::create([
                    'intSubmission_ID' => $submission->intSubmission_ID,
                    'txtStatus' => $newStatus,
                    'txtInReview' => $txtInReview,
                    'txtInsertedBy' => auth()->user()->txtFullName,
                    'dtmInserted' => now(),
                    'bitActive' => 1
                ]);
            }

            if ($request->action === 'reject' && $request->filled('txtCatatan')) {
                $submission->chats()->create([
                    'intUser_ID' => auth()->id(),
                    'txtMessage' => 'Catatan Penolakan: ' . $request->txtCatatan,
                ]);
            }

            DB::commit();

            $message = $request->action === 'approve'
                ? 'Pengajuan surat berhasil disetujui!'
                : 'Pengajuan surat berhasil ditolak!';

            return redirect()->route('akademik.submissions.preview', $submission->intSubmission_ID)->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memproses pengajuan: ' . $e->getMessage());
        }
    }

    private function renderLetterTemplate($templatePath, $data, $submission)
    {
        try {
            // Cek apakah template exists
            if (!View::exists($templatePath)) {
                throw new \Exception("Template tidak ditemukan: {$templatePath}");
            }

            return view($templatePath, compact('data', 'submission'))->render();

        } catch (\Exception $e) {
            // Fallback ke template default
            return view('templates.default_letter', compact('data', 'submission'))->render();
        }
    }

    public function editSubmission($submissionId)
    {
        try {
            $submission = Submission::with(['letterType.letterFields' => function ($q) {
                $q->where('bitActive', 1)
                ->where('txtFieldType', '!=', 'file')
                ->orderBy('intFieldOrder');
            }, 'values.letterField'])->findOrFail($submissionId);

            // Kumpulkan nilai yang sudah ada (hanya untuk field non-file)
            $currentValues = [];
            foreach ($submission->values as $value) {
                $fieldName = $value->letterField->txtFieldName ?? $value->txtFieldName;
                $fieldType = $value->letterField->txtFieldType ?? null;

                // Hanya ambil nilai untuk field non-file
                if ($fieldType !== 'file') {
                    $currentValues[$fieldName] = $value->txtFieldValue;
                }
            }

            // Siapkan opsi select per field
            $fieldOptions = [];
            foreach ($submission->letterType->letterFields as $f) {
                if ($f->txtFieldType === 'select') {
                    $opts = [];
                    $cfg = $this->decodeJson($f->jsonFieldOptions);

                    if (($cfg['source'] ?? 'static') === 'static') {
                        $opts = $cfg['options'] ?? [];
                    } else {
                        // Untuk dynamic options, bisa disesuaikan sesuai kebutuhan
                        $opts = $this->buildDynamicOptions($cfg);
                    }

                    $fieldOptions[$f->txtFieldName] = $opts;
                }
            }

            return view('pages.submissions.akademik.components._edit_fields', [
                'letterType' => $submission->letterType,
                'fieldOptions' => $fieldOptions,
                'currentValues' => $currentValues,
                'submission' => $submission
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in editSubmission: ' . $e->getMessage(), [
                'submission_id' => $submissionId,
                'trace' => $e->getTraceAsString()
            ]);

            return response('<div class="alert alert-danger">Gagal memuat formulir edit: ' . $e->getMessage() . '</div>', 500);
        }
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

    public function updateSubmission(Request $request, $submissionId)
    {
        try {
            $submission = Submission::with(['letterType.letterFields' => function ($q) {
                $q->where('bitActive', 1)->orderBy('intFieldOrder');
            }, 'values'])->findOrFail($submissionId);

            // Filter hanya field yang bukan file untuk validasi
            $editableFields = $submission->letterType->letterFields->filter(function($field) {
                return $field->txtFieldType !== 'file';
            });

            // Bangun rules validasi hanya untuk field yang bisa diedit (non-file)
            [$rules, $selectInMap] = $this->buildValidationRulesForEdit($editableFields);
            $validated = $request->validate($rules);

            DB::beginTransaction();

            // Update nilai field yang sudah ada (hanya field non-file)
            $fieldInputs = $request->input('fields', []);

            foreach ($editableFields as $field) {
                $name = $field->txtFieldName;
                $type = $field->txtFieldType;
                $value = Arr::get($fieldInputs, $name);

                // Cari submission value yang sudah ada
                $existingValue = $submission->values->where('txtFieldName', $name)->first();

                // Update atau create submission value (hanya untuk field non-file)
                if ($existingValue) {
                    $existingValue->update([
                        'txtFieldValue' => is_array($value) ? json_encode($value) : $value,
                        'txtUpdatedBy' => auth()->user()->txtFullName ?? 'System',
                        'dtmUpdated' => now(),
                    ]);
                } else {
                    // Jika belum ada record, buat baru
                    SubmissionValue::create([
                        'intSubmission_ID' => $submission->intSubmission_ID,
                        'intLetterField_ID' => $field->intLetterField_ID,
                        'txtFieldName' => $name,
                        'txtFieldLabel' => $field->txtFieldLabel,
                        'txtFieldType' => $type,
                        'txtFieldValue' => is_array($value) ? json_encode($value) : $value,
                        'jsonFieldMeta' => null, // non-file tidak perlu meta
                        'bitActive' => 1,
                        'txtInsertedBy' => auth()->user()->txtFullName ?? 'System',
                        'dtmInserted' => now(),
                        'txtUpdatedBy' => auth()->user()->txtFullName ?? 'System',
                        'dtmUpdated' => now(),
                    ]);
                }
            }

            // Update submission timestamp
            $submission->update([
                'txtUpdatedBy' => auth()->user()->txtFullName ?? 'System',
                'dtmUpdated' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data submission berhasil diperbarui.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui data: ' . $e->getMessage()
            ], 500);
        }
    }

    // Method baru khusus untuk validasi field yang bisa diedit
    private function buildValidationRulesForEdit($fields): array
    {
        $rules = ['fields' => 'required|array'];
        $selectIn = [];

        foreach ($fields as $f) {
            $name = $f->txtFieldName;
            $type = $f->txtFieldType;
            $extra = $this->decodeJson($f->jsonFieldValidation);
            $req = (int)$f->bitRequired === 1;

            $r = [];
            $r[] = $req ? 'required' : 'nullable';

            switch ($type) {
                case 'text':
                case 'textarea':
                    $r[] = 'string';
                    if (isset($extra['maxlength'])) $r[] = 'max:'.$extra['maxlength'];
                    if (isset($extra['minlength'])) $r[] = 'min:'.$extra['minlength'];
                    if (isset($extra['pattern'])) $r[] = 'regex:'.$extra['pattern'];
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
                        if ($allowed === array_values($allowed)) {
                            $allowed = $opts['options'] ?? [];
                        }
                        if (!empty($allowed)) {
                            $selectIn[$name] = $allowed;
                            $r[] = 'in:'.implode(',', array_map(fn($v) => str_replace(',', '\,', $v), $allowed));
                        }
                    }
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

    public function printLetter($submissionId)
    {
        try {
            $submission = Submission::with(['letterType', 'user.mahasiswaProfile.major', 'values.letterField'])
                ->findOrFail($submissionId);

            // Update status menjadi 'Sudah dicetak'
            $this->updateSubmissionToPrinted($submission);

            // Kumpulkan semua data dari submission values
            $submissionData = [];
            foreach ($submission->values as $value) {
                $fieldName = $value->letterField->txtFieldName ?? $value->txtFieldName;
                $submissionData[$fieldName] = $value->txtFieldValue;
            }

            $templatePath = $submission->letterType->txtTemplatePath ?? 'templates.default_letter';

            if (View::exists($templatePath)) {
                return view($templatePath, [
                    'data' => $submissionData,
                    'submission' => $submission,
                    'isPrint' => true // flag untuk print
                ]);
            } else {
                return redirect()->route('akademik.submissions.preview', $submissionId)
                    ->with('error', 'Template surat tidak ditemukan.');
            }

        } catch (\Exception $e) {
            return redirect()->route('akademik.submissions.preview', $submissionId)
                ->with('error', 'Gagal memuat halaman print: ' . $e->getMessage());
        }
    }

    public function downloadLetter($submissionId)
    {
        try {
            $submission = Submission::with(['letterType', 'user.mahasiswaProfile.major', 'values.letterField'])
                ->findOrFail($submissionId);

            // Update status menjadi 'Sudah dicetak'
            $this->updateSubmissionToPrinted($submission);

            // Kumpulkan semua data dari submission values
            $submissionData = [];
            foreach ($submission->values as $value) {
                $fieldName = $value->letterField->txtFieldName ?? $value->txtFieldName;
                $submissionData[$fieldName] = $value->txtFieldValue;
            }

            $templatePath = $submission->letterType->txtTemplatePath ?? 'templates.default_letter';

            // Generate PDF menggunakan DomPDF
            $pdf = Pdf::loadView($templatePath, [
                'data' => $submissionData,
                'submission' => $submission,
                'isPdf' => true // flag untuk PDF
            ]);

            $pdf->setPaper('A4', 'portrait');

            $filename = 'Surat_' . ($submission->letterType->txtCode ?? 'Letter') . '_' .
                    $submission->letterType->txtCode . '_' . date('Y-m-d') . '.pdf';

            return $pdf->download($filename);

        } catch (\Exception $e) {
            return redirect()->route('akademik.submissions.preview', $submissionId)
                ->with('error', 'Gagal mendownload surat: ' . $e->getMessage());
        }
    }

    private function updateSubmissionToPrinted($submission)
    {
        try {
            DB::beginTransaction();

            $oldStatus = $submission->txtStatus;

            // Update status submission
            $submission->update([
                'txtStatus' => 'Sudah dicetak',
                'txtUpdatedBy' => auth()->user()->txtFullName ?? 'System',
                'dtmUpdated' => now(),
            ]);

            $newStatus = $submission->txtStatus;

            if ($oldStatus !== $newStatus) {
                // Set status lama jadi tidak aktif
                SubmissionStatus::where('intSubmission_ID', $submission->intSubmission_ID)
                    ->where('bitActive', 1)
                    ->update([
                        'bitActive' => 0,
                        'txtUpdatedBy' => auth()->user()->txtFullName ?? 'System',
                        'dtmUpdated' => now()
                    ]);

                // Buat status baru
                SubmissionStatus::create([
                    'intSubmission_ID' => $submission->intSubmission_ID,
                    'txtStatus' => 'Sudah dicetak',
                    'txtInReview' => 'Proses TTD Basah',
                    'txtInsertedBy' => auth()->user()->txtFullName ?? 'System',
                    'dtmInserted' => now(),
                    'bitActive' => 1
                ]);
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}

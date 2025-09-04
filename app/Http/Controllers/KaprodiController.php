<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use Illuminate\Http\Request;
use App\Models\SubmissionValue;
use App\Models\SubmissionStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class KaprodiController extends Controller
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
        $majorId = optional(auth()->user()->dosenProfile)->intMajor_ID;

        $count = Submission::where('submissions.bitActive', 1) // Tambahkan alias tabel
            ->whereIn('submissions.txtStatus', $statusConditions)
            ->when($majorId, function($q) use ($majorId) {
                $q->join('users', 'submissions.intUser_ID', '=', 'users.intUser_ID')
                  ->join('mahasiswa_profiles', 'users.intUser_ID', '=', 'mahasiswa_profiles.intUser_ID')
                  ->where('mahasiswa_profiles.intMajor_ID', $majorId);
            })
            ->when($type === 'ujian', function($q) {
                $q->join('letter_types as lt2', 'submissions.intLetterType_ID', '=', 'lt2.intLetterType_ID')
                  ->where('lt2.bitUjian', 1);
            })
            ->when($type === 'surat', function($q) {
                $q->join('letter_types as lt3', 'submissions.intLetterType_ID', '=', 'lt3.intLetterType_ID')
                  ->where('lt3.bitUjian', 0);
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

        return view('pages.submissions.kaprodi.index', $pageData);
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
            ->where('submissions.bitActive', 1) // Tambahkan alias tabel
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
            // Kaprodi hanya bisa melihat submission dengan status "Sedang ditinjau Kaprodi"
            return ['Sedang ditinjau Kaprodi'];
        } else { // selesai
            // Kaprodi bisa melihat submission yang sudah disetujui atau ditolak oleh kaprodi
            return ['Disetujui Kaprodi', 'Ditolak Kaprodi', 'Disetujui Akademik', 'Ditolak Akademik', 'Sudah dicetak'];
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
        $statusText = $status === 'proses' ? 'yang sedang menunggu persetujuan kaprodi' : 'yang sudah diproses kaprodi';
        return "Sistem Informasi Pembuatan {$typeText} {$statusText}";
    }

    private function getAlertMessage($type, $status, $count)
    {
        if ($status === 'proses') {
            $typeText = $type === 'surat' ? 'surat' : 'ujian';
            return "{$count} permintaan {$typeText} menunggu persetujuan Anda!";
        } else {
            $typeText = $type === 'surat' ? 'surat' : 'ujian';
            return "{$count} permintaan {$typeText} sudah diproses.";
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

        // Tombol Proses - hanya untuk status proses dan jika statusnya "Sedang ditinjau Kaprodi"
        if ($status === 'proses' && ($r->txtStatus ?? null) === 'Sedang ditinjau Kaprodi') {
            $buttons .= '<a href="'.route('kaprodi.submissions.preview', $r->intSubmission_ID).'"
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
                return '<button class="btn btn-sm btn-success rounded-pill show-status-modal"
                            data-bs-toggle="modal" data-bs-target="#submissionModal"
                            data-submissions-id="'.$r->intSubmission_ID.'" data-type-name="'.$r->letter_type.'">
                            Disetujui Kaprodi</button>';
            case 'Ditolak Kaprodi':
                return '<button class="btn btn-sm btn-danger rounded-pill show-status-modal"
                            data-bs-toggle="modal" data-bs-target="#submissionModal"
                            data-submissions-id="'.$r->intSubmission_ID.'" data-type-name="'.$r->letter_type.'">
                            Ditolak Kaprodi</button>';
            case 'Disetujui Akademik':
                return '<button class="btn btn-sm btn-info rounded-pill show-status-modal"
                            data-bs-toggle="modal" data-bs-target="#submissionModal"
                            data-submissions-id="'.$r->intSubmission_ID.'" data-type-name="'.$r->letter_type.'">
                            Disetujui Akademik</button>';
            case 'Ditolak Akademik':
                return '<button class="btn btn-sm btn-warning rounded-pill show-status-modal"
                            data-bs-toggle="modal" data-bs-target="#submissionModal"
                            data-submissions-id="'.$r->intSubmission_ID.'" data-type-name="'.$r->letter_type.'">
                            Ditolak Akademik</button>';
            case 'Sudah dicetak':
                return '<button class="btn btn-sm btn-secondary rounded-pill show-status-modal"
                            data-bs-toggle="modal" data-bs-target="#submissionModal"
                            data-submissions-id="'.$r->intSubmission_ID.'" data-type-name="'.$r->letter_type.'">
                            Sudah dicetak</button>';
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
            // Kaprodi hanya bisa melihat submission dengan status "Sedang ditinjau Kaprodi"
            $submission = Submission::with(['letterType', 'statuses', 'user.mahasiswaProfile.major', 'values.letterField'])
                ->where('submissions.txtStatus', 'Sedang ditinjau Kaprodi') // Tambahkan alias tabel
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

            return view('pages.submissions.kaprodi.preview', [
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
                ->where('submissions.txtStatus', 'Sedang ditinjau Kaprodi') // Tambahkan alias tabel
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
            'txtCatatan' => 'required_if:action,reject|nullable|string|max:1000',
        ]);

        try {
            $submission_statuses = SubmissionStatus::where('intSubmission_ID', $submissionId)->where('bitActive', 1)->first();
            DB::beginTransaction();

            $submission = Submission::findOrFail($submissionId);

            $newStatus = $request->action === 'approve' ? 'Disetujui Kaprodi' : 'Ditolak Kaprodi';

            $submission->update([
                'txtStatus' => $newStatus,
                'dtmKaprodiProcessed' => now(),
                'txtUpdatedBy' => auth()->user()->txtFullName,
                'dtmUpdated' => now()
            ]);

            $submission_statuses->update([
                'bitActive' => 0,
                'txtUpdatedBy' => auth()->user()->txtFullName,
                'dtmUpdated' => now()
            ]);

            $txtInReview = $newStatus === 'Disetujui Kaprodi' ? 'Persetujuan Akademik' : 'Ditolak Kaprodi';

            SubmissionStatus::create([
                'intSubmission_ID' => $submission->intSubmission_ID,
                'txtStatus' => $newStatus,
                'txtInReview' => $txtInReview,
                'txtInsertedBy' => auth()->user()->txtFullName,
                'dtmInserted' => now(),
                'bitActive' => 1
            ]);

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

            return redirect()->route('kaprodi.submissions.index', ['type' => 'surat', 'status' => 'selesai'])
                         ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memproses pengajuan: ' . $e->getMessage());
        }
    }

    private function renderLetterTemplate($templatePath, $data, $submission)
    {
        try {
            if (!View::exists($templatePath)) {
                throw new \Exception("Template tidak ditemukan: {$templatePath}");
            }

            return view($templatePath, compact('data', 'submission'))->render();

        } catch (\Exception $e) {
            return view('templates.default_letter', compact('data', 'submission'))->render();
        }
    }
}

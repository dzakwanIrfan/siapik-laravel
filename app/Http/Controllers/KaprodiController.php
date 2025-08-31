<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Models\SubmissionValue;
use Illuminate\Http\Request;

class KaprodiController extends Controller
{
    public function index()
    {
        return view('pages.submissions.kaprodi.index');
    }

    public function indexDatatable()
    {
        $majorId = optional(auth()->user()->dosenProfile)->intMajor_ID;

        $query = Submission::query()
            ->join('letter_types', 'submissions.intLetterType_ID', '=', 'letter_types.intLetterType_ID')
            ->join('users', 'submissions.intUser_ID', '=', 'users.intUser_ID')
            ->join('mahasiswa_profiles', 'users.intUser_ID', '=', 'mahasiswa_profiles.intUser_ID')
            ->when($majorId, fn ($q) => $q->where('mahasiswa_profiles.intMajor_ID', $majorId))
            ->where('submissions.bitActive', 1)
            ->select([
                'submissions.*',
                'letter_types.txtNameLetterType as letter_type',
                'users.txtFullName as user_full_name',
            ]);

        return \DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('letter_type', fn($row) => $row->letter_type ?? '-')
            ->editColumn('dtmInserted', fn($row) => $row->dtmInserted ?? '-')
            ->addColumn('action', function ($r) {
                return '<div class="d-flex gap-1" role="group">
                            <button type="button" class="btn btn-info btn-sm rounded-pill icon show-attachment-modal" data-bs-toggle="tooltip" data-bs-placement="top" title="Cek bukti lampiran" data-submission-id="'.$r->intSubmission_ID.'"><i class="fas fa-eye"></i></button>
                        </div>';
            })
            ->addColumn('status', function ($r) {
                if (($r->txtStatus ?? null) === 'Sedang ditinjau Kaprodi') {
                    return '<button 
                                class="btn btn-sm btn-primary rounded-pill show-status-modal"
                                data-bs-toggle="modal"
                                data-bs-target="#submissionModal"
                                data-submissions-id="'.$r->intSubmission_ID.'"
                                data-type-name="'.$r->letter_type.'"
                            >Sedang ditinjau Kaprodi</button>';
                }
                return e($r->txtStatus ?? '-');
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
}
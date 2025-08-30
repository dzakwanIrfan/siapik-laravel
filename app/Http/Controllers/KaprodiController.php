<?php

namespace App\Http\Controllers;

use App\Models\Submission;
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
                return '<div class="btn-group" role="group">
                            <button type="button" class="btn btn-info btn-action btn-view"><i class="fas fa-eye"></i></button>
                            <button type="button" class="btn btn-warning btn-action btn-edit"><i class="fas fa-edit"></i></button>
                            <button type="button" class="btn btn-danger btn-action btn-delete"><i class="fas fa-trash-alt"></i></button>
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

}

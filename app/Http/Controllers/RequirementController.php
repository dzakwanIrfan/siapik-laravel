<?php

namespace App\Http\Controllers;

use App\Models\Requirement;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class RequirementController extends Controller
{
    public function index()
    {
        $requirements = Requirement::all();
        return view('master.requirement', compact('requirements'));
    }

    public function data(Request $request)
    {
        $requirements = Requirement::query();

        return DataTables::of($requirements)
            ->addColumn('action', function ($requirement) {
                // Tombol aksi (edit dan hapus)
                $editBtn = '<a href="javascript:void(0)" class="btn btn-warning btn-sm edit-btn" data-id="' . $requirement->intRequirement_ID . '">Edit</a>';
                $deleteBtn = '<a href="javascript:void(0)" class="btn btn-danger btn-sm delete-btn" data-id="' . $requirement->intRequirement_ID . '">Hapus</a>';
                return $editBtn . ' ' . $deleteBtn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function store(Request $request)
    {
        // Validasi
        error_log('Request data: ' . json_encode($request->all()));
        Validator::make($request->all(), [
            'txtNameRequirement' => 'required|string|max:255',
        ])->validate();

        $dataToStore = $request->all();
        $dataToStore['txtInsertedBy'] = Auth::user()->txtFullName;

        $requirement = Requirement::create($dataToStore);

        return response()->json(['success' => 'Requirement berhasil ditambahkan.']);
    }

    public function edit(Requirement $requirement)
    {
        return response()->json($requirement);
    }

    public function update(Request $request, Requirement $requirement)
    {
        Validator::make($request->all(), [
            'txtNameRequirement' => 'required|string|max:255',
        ])->validate();

        $requirementData = $request->all();
        $requirementData['txtUpdatedBy'] = Auth::user()->txtFullName;

        $requirement->update($requirementData);

        return response()->json(['success' => 'Requirement berhasil diperbarui.']);
    }

    public function destroy(Requirement $requirement)
    {
        $requirement->delete();
        return response()->json(['success' => 'Requirement berhasil dihapus.']);
    }
}

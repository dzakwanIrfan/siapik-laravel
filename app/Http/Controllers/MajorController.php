<?php

namespace App\Http\Controllers;

use App\Models\Major;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class MajorController extends Controller
{
    public function index()
    {
        $majors = Major::all();
        return view('master.major', compact('majors'));
    }

    public function data(Request $request)
    {
        $majors = Major::query();

        return DataTables::of($majors)
            ->addColumn('action', function ($major) {
                // Tombol aksi (edit dan hapus)
                $viewBtn = '<a href="javascript:void(0)" class="btn btn-info btn-sm view-btn" data-id="' . $major->intMajor_ID . '"><i class="fas fa-eye"></i></a>';
                $editBtn = '<a href="javascript:void(0)" class="btn btn-warning btn-sm edit-btn" data-id="' . $major->intMajor_ID . '"><i class="fas fa-edit"></i></a>';
                $deleteBtn = '<a href="javascript:void(0)" class="btn btn-danger btn-sm delete-btn" data-id="' . $major->intMajor_ID . '"><i class="fas fa-trash-alt"></i></a>';
                return '<div class="d-flex gap-2">' . $viewBtn . $editBtn . $deleteBtn . '</div>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function store(Request $request)
    {
        // Validasi
        error_log('Request data: ' . json_encode($request->all()));
        Validator::make($request->all(), [
            'txtNameMajor' => 'required|string|max:255',
            'txtStrata' => 'required|string|max:255',
            'txtTitle' => 'required|string|max:255',
            'txtShortTitle' => 'required|string|max:255',
        ])->validate();

        $dataToStore = $request->all();
        $dataToStore['txtInsertedBy'] = Auth::user()->txtFullName;

        $major = Major::create($dataToStore);

        return response()->json(['success' => 'Major berhasil ditambahkan.']);
    }

    public function edit(Major $major)
    {
        return response()->json($major);
    }

    public function update(Request $request, Major $major)
    {
        Validator::make($request->all(), [
            'txtNameMajor' => 'required|string|max:255',
            'txtStrata' => 'required|string|max:255',
            'txtTitle' => 'required|string|max:255',
            'txtShortTitle' => 'required|string|max:255',
        ])->validate();

        $majorData = $request->all();
        $majorData['txtUpdatedBy'] = Auth::user()->txtFullName;

        $major->update($majorData);

        return response()->json(['success' => 'Major berhasil diperbarui.']);
    }

    public function destroy(Major $major)
    {
        $major->delete();
        return response()->json(['success' => 'Major berhasil dihapus.']);
    }
}

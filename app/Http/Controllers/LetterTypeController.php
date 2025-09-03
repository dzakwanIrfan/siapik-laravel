<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LetterType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\Controller;

class LetterTypeController extends Controller
{
    public function index()
    {
        return view('master.letter_types');
    }

    public function data()
    {
        $letterTypes = LetterType::query();
        return DataTables::of($letterTypes)
            ->addColumn('action', function ($letterType) {
                $viewBtn = '<a href="javascript:void(0)" class="btn btn-info btn-sm view-btn" data-id="' . $letterType->intLetterType_ID . '"><i class="fas fa-eye"></i></a>';
                $editBtn = '<a href="javascript:void(0)" class="btn btn-warning btn-sm edit-btn" data-id="' . $letterType->intLetterType_ID . '"><i class="fas fa-edit"></i></a>';
                $deleteBtn = '<a href="javascript:void(0)" class="btn btn-danger btn-sm delete-btn" data-id="' . $letterType->intLetterType_ID . '"><i class="fas fa-trash-alt"></i></a>';
                return '<div class="d-flex gap-2">' . $viewBtn . $editBtn . $deleteBtn . '</div>';
            })
            ->editColumn('bitActive', function ($letterType) {
                return $letterType->bitActive ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-danger">Tidak Aktif</span>';
            })
            ->rawColumns(['action', 'bitActive'])
            ->make(true);
    }

    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'txtNameLetterType' => 'required|string|max:255',
            'txtCode' => 'required|string|unique:letter_types,txtCode',
            'txtDescription' => 'required|string',
            'txtTemplatePath' => 'required|string',
            'bitActive' => 'required|boolean',
        ])->validate();

        LetterType::create([
            'txtNameLetterType' => $request->txtNameLetterType,
            'txtCode' => $request->txtCode,
            'txtDescription' => $request->txtDescription,
            'txtTemplatePath' => $request->txtTemplatePath,
            'bitActive' => $request->bitActive,
            'txtInsertedBy' => Auth::user()->txtFullName,
            'txtInserted' => now(),
        ]);

        return response()->json(['success' => 'Jenis Surat berhasil ditambahkan.']);
    }

    public function edit(LetterType $letter_type)
    {
        return response()->json($letter_type);
    }

    public function update(Request $request, LetterType $letter_type)
    {
        Validator::make($request->all(), [
            'txtNameLetterType' => 'required|string|max:255',
            'txtCode' => 'required|string|unique:letter_types,txtCode,' . $letter_type->intLetterType_ID . ',intLetterType_ID',
            'txtDescription' => 'required|string',
            'txtTemplatePath' => 'required|string',
            'bitActive' => 'required|boolean',
        ])->validate();

        $letter_type->update([
            'txtNameLetterType' => $request->txtNameLetterType,
            'txtCode' => $request->txtCode,
            'txtDescription' => $request->txtDescription,
            'txtTemplatePath' => $request->txtTemplatePath,
            'bitActive' => $request->bitActive,
            'txtUpdatedBy' => Auth::user()->txtFullName,
            'txtUpdated' => now(),
        ]);

        return response()->json(['success' => 'Jenis Surat berhasil diperbarui.']);
    }

    public function destroy(LetterType $letter_type)
    {
        $letter_type->delete();
        return response()->json(['success' => 'Jenis Surat berhasil dihapus.']);
    }
}

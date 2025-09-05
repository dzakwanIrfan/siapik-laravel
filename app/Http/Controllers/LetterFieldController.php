<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LetterField;
use App\Models\LetterType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class LetterFieldController extends Controller
{
    public function index()
    {
        $letterTypes = LetterType::where('bitActive', 1)->get();
        return view('master.letter_fields', compact('letterTypes'));
    }

    public function data()
    {
        $letterFields = LetterField::with('letterType');
        return DataTables::of($letterFields)
            ->addColumn('action', function ($field) {
                $viewBtn = '<a href="javascript:void(0)" class="btn btn-info btn-sm view-btn" data-id="' . $field->intLetterField_ID . '"><i class="fas fa-eye"></i></a>';
                $editBtn = '<a href="javascript:void(0)" class="btn btn-warning btn-sm edit-btn" data-id="' . $field->intLetterField_ID . '"><i class="fas fa-edit"></i></a>';
                $deleteBtn = '<a href="javascript:void(0)" class="btn btn-danger btn-sm delete-btn" data-id="' . $field->intLetterField_ID . '"><i class="fas fa-trash-alt"></i></a>';
                return '<div class="d-flex gap-2">' . $viewBtn . $editBtn . $deleteBtn . '</div>';
            })
            ->editColumn('bitActive', function ($field) {
                return $field->bitActive ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-danger">Tidak Aktif</span>';
            })
            ->rawColumns(['action', 'bitActive'])
            ->make(true);
    }

    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'intLetterType_ID' => 'required|exists:letter_types,intLetterType_ID',
            'txtFieldName' => 'required|string|max:255',
            'txtFieldLabel' => 'required|string|max:255',
            'txtFieldType' => 'required|in:text,textarea,select,date,file,number',
            'bitRequired' => 'required|boolean',
            'intFieldOrder' => 'required|integer',
            'bitActive' => 'required|boolean',
        ])->validate();

        LetterField::create([
            'intLetterType_ID' => $request->intLetterType_ID,
            'txtFieldName' => $request->txtFieldName,
            'txtFieldLabel' => $request->txtFieldLabel,
            'txtFieldType' => $request->txtFieldType,
            'jsonFieldOptions' => $request->jsonFieldOptions,
            'bitRequired' => $request->bitRequired,
            'intFieldOrder' => $request->intFieldOrder,
            'jsonFieldValidation' => $request->jsonFieldValidation,
            'bitActive' => $request->bitActive,
            'txtInsertedBy' => Auth::user()->txtFullName,
            'txtInserted' => now(),
        ]);

        return response()->json(['success' => 'Kolom Isian berhasil ditambahkan.']);
    }

    public function edit(LetterField $letter_field)
    {
        return response()->json($letter_field);
    }

    public function update(Request $request, LetterField $letter_field)
    {
        Validator::make($request->all(), [
            'intLetterType_ID' => 'required|exists:letter_types,intLetterType_ID',
            'txtFieldName' => 'required|string|max:255',
            'txtFieldLabel' => 'required|string|max:255',
            'txtFieldType' => 'required|in:text,textarea,select,date,file,number',
            'bitRequired' => 'required|boolean',
            'intFieldOrder' => 'required|integer',
            'bitActive' => 'required|boolean',
        ])->validate();

        $letter_field->update([
            'intLetterType_ID' => $request->intLetterType_ID,
            'txtFieldName' => $request->txtFieldName,
            'txtFieldLabel' => $request->txtFieldLabel,
            'txtFieldType' => $request->txtFieldType,
            'jsonFieldOptions' => $request->jsonFieldOptions,
            'bitRequired' => $request->bitRequired,
            'intFieldOrder' => $request->intFieldOrder,
            'jsonFieldValidation' => $request->jsonFieldValidation,
            'bitActive' => $request->bitActive,
            'txtUpdatedBy' => Auth::user()->txtFullName,
            'txtUpdated' => now(),
        ]);

        return response()->json(['success' => 'Kolom Isian berhasil diperbarui.']);
    }

    public function destroy(LetterField $letter_field)
    {
        $letter_field->delete();
        return response()->json(['success' => 'Kolom Isian berhasil dihapus.']);
    }
}

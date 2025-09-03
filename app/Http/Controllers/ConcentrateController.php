<?php

namespace App\Http\Controllers;

use App\Models\Major;
use App\Models\Concentrate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class ConcentrateController extends Controller
{
    public function index()
    {
        $majors = Major::all();
        return view('master.concentrates', compact('majors'));
    }

    public function data()
    {
        $concentrates = Concentrate::with('major');
        return DataTables::of($concentrates)
            ->addColumn('action', function ($concentrate) {
                $viewBtn = '<a href="javascript:void(0)" class="btn btn-info btn-sm view-btn" data-id="' . $concentrate->intConcentrate_ID . '"><i class="fas fa-eye"></i></a>';
                $editBtn = '<a href="javascript:void(0)" class="btn btn-warning btn-sm edit-btn" data-id="' . $concentrate->intConcentrate_ID . '"><i class="fas fa-edit"></i></a>';
                $deleteBtn = '<a href="javascript:void(0)" class="btn btn-danger btn-sm delete-btn" data-id="' . $concentrate->intConcentrate_ID . '"><i class="fas fa-trash-alt"></i></a>';
                return '<div class="d-flex gap-2">' . $viewBtn . $editBtn . $deleteBtn . '</div>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'intMajor_ID' => 'required|exists:majors,intMajor_ID',
            'txtNameConcentrate' => 'required|string|max:255|unique:concentrates,txtNameConcentrate',
        ])->validate();

        Concentrate::create([
            'intMajor_ID' => $request->intMajor_ID,
            'txtNameConcentrate' => $request->txtNameConcentrate,
            'txtInsertedBy' => Auth::user()->txtFullName,
        ]);

        return response()->json(['success' => 'Peminatan berhasil ditambahkan.']);
    }

    public function edit(Concentrate $concentrate)
    {
        $concentrate->load('major');
        return response()->json($concentrate);
    }

    public function update(Request $request, Concentrate $concentrate)
    {
        Validator::make($request->all(), [
            'intMajor_ID' => 'required|exists:majors,intMajor_ID',
            'txtNameConcentrate' => 'required|string|max:255|unique:concentrates,txtNameConcentrate,' . $concentrate->intConcentrate_ID . ',intConcentrate_ID',
        ])->validate();

        $concentrate->update([
            'intMajor_ID' => $request->intMajor_ID,
            'txtNameConcentrate' => $request->txtNameConcentrate,
            'txtUpdatedBy' => Auth::user()->txtFullName,
        ]);

        return response()->json(['success' => 'Peminatan berhasil diperbarui.']);
    }

    public function destroy(Concentrate $concentrate)
    {
        $concentrate->delete();
        return response()->json(['success' => 'Peminatan berhasil dihapus.']);
    }
}

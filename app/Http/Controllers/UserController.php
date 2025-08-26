<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        $roles = Role::pluck('name', 'name')->all();
        return view('master.user', compact('roles'));
    }

    public function data(Request $request)
    {
        $users = User::with('roles');

        if ($request->has('role')) {
            $roleName = $request->input('role');

            // Filter user berdasarkan role menggunakan whereHas() dari Spatie
            $users->whereHas('roles', function ($query) use ($roleName) {
                $query->where('name', $roleName);
            });
        }
        return DataTables::of($users)
            ->addColumn('role', function ($user) {
                return $user->roles->pluck('name')->implode(', ');
            })
            ->addColumn('action', function ($user) {
                // Tombol aksi (edit dan hapus)
                $editBtn = '<a href="javascript:void(0)" class="btn btn-warning btn-sm edit-btn" data-id="' . $user->intUser_ID . '">Edit</a>';
                $deleteBtn = '<a href="javascript:void(0)" class="btn btn-danger btn-sm delete-btn" data-id="' . $user->intUser_ID . '">Hapus</a>';
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
            'txtFullName' => 'required|string|max:255',
            'txtEmail' => 'required|email|unique:users,txtEmail',
            'txtBirthPlace' => 'required|string|max:255', // Wajib diisi
            'dtmBirthDate' => 'required|date',         // Wajib diisi
            'role' => 'required',
            'txtPassword' => 'required|min:8',
        ])->validate();

        $dataToStore = $request->all();
        $dataToStore['txtPassword'] = Hash::make($request->txtPassword);
        $dataToStore['txtInsertedBy'] = Auth::user()->txtFullName;

        $user = User::create($dataToStore);
        $user->assignRole($request->role);

        return response()->json(['success' => 'User berhasil ditambahkan.']);
    }

    public function edit(User $user)
    {
        // Eager load roles
        $user->load('roles');
        return response()->json($user);
    }

    public function update(Request $request, User $user)
    {
        Validator::make($request->all(), [
        'txtFullName' => 'required|string|max:255',
        'txtEmail' => 'required|email|unique:users,txtEmail,' . $user->intUser_ID . ',intUser_ID',
        'txtBirthPlace' => 'required|string|max:255',
        'dtmBirthDate' => 'required|date',
        'role' => 'required',
        'txtPassword' => 'nullable|min:8',
        ])->validate();

        $userData = $request->except('txtPassword');
        if ($request->filled('txtPassword')) {
            $userData['txtPassword'] = Hash::make($request->txtPassword);
        }
        $userData['txtUpdatedBy'] = Auth::user()->txtFullName; // Mengambil nama user yang login

        $user->update($userData);
        $user->syncRoles($request->role);

        return response()->json(['success' => 'User berhasil diperbarui.']);
    }

    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(['success' => 'User berhasil dihapus.']);
    }
}

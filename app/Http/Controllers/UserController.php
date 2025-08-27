<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Major;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $majors = Major::all();
        return view('master.user', compact('roles', 'majors'));
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
        // 1. Validasi data user dasar
        $userValidator = Validator::make($request->all(), [
            'txtFullName' => 'required|string|max:255',
            'txtEmail' => 'required|email|unique:users,txtEmail',
            'txtBirthPlace' => 'required|string|max:255',
            'dtmBirthDate' => 'required|date',
            'role' => 'required',
            'txtPassword' => 'required|min:8',
        ]);

        // Jika validasi user gagal, lempar exception
        if ($userValidator->fails()) {
            return response()->json(['errors' => $userValidator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $user = User::create([
                'txtFullName' => $request->txtFullName,
                'txtEmail' => $request->txtEmail,
                'txtBirthPlace' => $request->txtBirthPlace,
                'dtmBirthDate' => $request->dtmBirthDate,
                'txtPassword' => Hash::make($request->txtPassword),
                'txtInsertedBy' => Auth::user()->txtFullName,
            ]);

            $user->assignRole($request->role);

            // 2. Validasi dan simpan profil mahasiswa jika role-nya 'mahasiswa'
            if ($request->role == 'mahasiswa') {
                $profileValidator = Validator::make($request->all(), [
                    'txtNIM' => 'required|string|unique:mahasiswa_profiles,txtNIM',
                    'intMajor_ID' => 'required|exists:majors,intMajor_ID',
                    'intConcentrate_ID' => 'required|exists:concentrates,intConcentrate_ID',
                ]);

                // Jika validasi profil gagal, batalkan transaksi dan kirim error
                if ($profileValidator->fails()) {
                    DB::rollBack();
                    return response()->json(['errors' => $profileValidator->errors()], 422);
                }

                $user->mahasiswaProfile()->create([
                    'txtNIM' => $request->txtNIM,
                    'txtYear' => $request->txtYear,
                    'intMajor_ID' => $request->intMajor_ID,
                    'intConcentrate_ID' => $request->intConcentrate_ID,
                    'txtInsertedBy' => Auth::user()->txtFullName,
                ]);
            }

            DB::commit();
            return response()->json(['success' => 'User berhasil ditambahkan.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function edit(User $user)
    {
        // Eager load roles
        $user->load('roles','mahasiswaProfile');
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

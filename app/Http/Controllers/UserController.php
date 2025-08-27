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

            if ($request->role == 'dosen') {
                $profileValidator = Validator::make($request->all(), [
                    'txtNIP' => 'required|string|unique:dosen_profiles,txtNIP',
                    'txtNIDN' => 'required|string|unique:dosen_profiles,txtNIDN',
                    'intMajor_ID_dosen' => 'required|exists:majors,intMajor_ID',
                ]);

                if ($profileValidator->fails()) {
                    DB::rollBack();
                    return response()->json(['errors' => $profileValidator->errors()], 422);
                }

                $user->dosenProfile()->create([
                    'txtNIP' => $request->txtNIP,
                    'txtNIDN' => $request->txtNIDN,
                    'txtFieldOfKnowledge' => $request->txtFieldOfKnowledge,
                    'intMajor_ID' => $request->intMajor_ID_dosen,
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
        $user->load('roles','mahasiswaProfile', 'dosenProfile');
        return response()->json($user);
    }

    public function update(Request $request, User $user)
    {
        // 1. Validasi data user dasar
        $userValidator = Validator::make($request->all(), [
            'txtFullName' => 'required|string|max:255',
            // Pastikan email unik, kecuali untuk user yang sedang diedit
            'txtEmail' => 'required|email|unique:users,txtEmail,' . $user->intUser_ID . ',intUser_ID',
            'txtBirthPlace' => 'required|string|max:255',
            'dtmBirthDate' => 'required|date',
            'role' => 'required',
            // Password bersifat opsional saat update
            'txtPassword' => 'nullable|min:8',
        ]);

        // Jika validasi user gagal, kirim respons error
        if ($userValidator->fails()) {
            return response()->json(['errors' => $userValidator->errors()], 422);
        }

        // 2. Gunakan transaction untuk keamanan data
        DB::beginTransaction();
        try {
            // Siapkan data user untuk diupdate
            $userData = [
                'txtFullName' => $request->txtFullName,
                'txtEmail' => $request->txtEmail,
                'txtBirthPlace' => $request->txtBirthPlace,
                'dtmBirthDate' => $request->dtmBirthDate,
                'txtUpdatedBy' => Auth::user()->txtFullName,
            ];

            // Hanya update password jika diisi
            if ($request->filled('txtPassword')) {
                $userData['txtPassword'] = Hash::make($request->txtPassword);
            }

            // Update data user
            $user->update($userData);

            // Sinkronkan role
            $user->syncRoles($request->role);

            // 3. Handle logika untuk profil mahasiswa
            if ($request->role == 'mahasiswa') {
                $profileValidator = Validator::make($request->all(), [
                    // Pastikan NIM unik, kecuali untuk profil mahasiswa yang sedang diedit
                    'txtNIM' => 'required|string|unique:mahasiswa_profiles,txtNIM,' . ($user->mahasiswaProfile->intMahasiswaProfile_ID ?? 'NULL') . ',intMahasiswaProfile_ID',
                    'intMajor_ID' => 'required|exists:majors,intMajor_ID',
                    'intConcentrate_ID' => 'required|exists:concentrates,intConcentrate_ID',
                ]);

                // Jika validasi profil gagal, batalkan transaksi
                if ($profileValidator->fails()) {
                    DB::rollBack();
                    return response()->json(['errors' => $profileValidator->errors()], 422);
                }

                // Gunakan updateOrCreate untuk membuat atau memperbarui profil
                $user->mahasiswaProfile()->updateOrCreate(
                    ['intUser_ID' => $user->intUser_ID], // Kondisi untuk mencari
                    [
                        // Data untuk diisi atau diperbarui
                        'txtNIM' => $request->txtNIM,
                        'txtYear' => $request->txtYear,
                        'intMajor_ID' => $request->intMajor_ID,
                        'intConcentrate_ID' => $request->intConcentrate_ID,
                        'txtUpdatedBy' => Auth::user()->txtFullName,
                    ]
                );
            } else {
                // Jika rolenya BUKAN mahasiswa, hapus profil jika ada
                if ($user->mahasiswaProfile) {
                    $user->mahasiswaProfile->delete();
                }
            }

            // LOGIKA BARU UNTUK DOSEN
            if ($request->role == 'dosen') {
                $profileValidator = Validator::make($request->all(), [
                    'txtNIP' => 'required|string|unique:dosen_profiles,txtNIP,' . ($user->dosenProfile->intDosenProfile_ID ?? 'NULL') . ',intDosenProfile_ID',
                    'txtNIDN' => 'required|string|unique:dosen_profiles,txtNIDN,' . ($user->dosenProfile->intDosenProfile_ID ?? 'NULL') . ',intDosenProfile_ID',
                    'intMajor_ID_dosen' => 'required|exists:majors,intMajor_ID',
                ]);

                if ($profileValidator->fails()) {
                    DB::rollBack();
                    return response()->json(['errors' => $profileValidator->errors()], 422);
                }

                $user->dosenProfile()->updateOrCreate(
                    ['intUser_ID' => $user->intUser_ID],
                    [
                        'txtNIP' => $request->txtNIP,
                        'txtNIDN' => $request->txtNIDN,
                        'txtFieldOfKnowledge' => $request->txtFieldOfKnowledge,
                        'intMajor_ID' => $request->intMajor_ID_dosen,
                        'txtUpdatedBy' => Auth::user()->txtFullName,
                    ]
                );
            } else {
                if ($user->dosenProfile) {
                    $user->dosenProfile->delete();
                }
            }

            DB::commit(); // Simpan semua perubahan jika berhasil
            return response()->json(['success' => 'User berhasil diperbarui.']);

        } catch (\Exception $e) {
            DB::rollBack(); // Batalkan semua jika ada error
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(['success' => 'User berhasil dihapus.']);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Major;
use App\Imports\DosenImport;
use App\Imports\UsersImport;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Imports\MahasiswaImport;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
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
                $viewBtn = '<a href="javascript:void(0)" class="btn btn-info btn-sm view-btn" data-id="' . $user->intUser_ID . '"><i class="fas fa-eye"></i></a>';
                $editBtn = '<a href="javascript:void(0)" class="btn btn-warning btn-sm edit-btn" data-id="' . $user->intUser_ID . '"><i class="fas fa-edit"></i></a>';
                $deleteBtn = '<a href="javascript:void(0)" class="btn btn-danger btn-sm delete-btn" data-id="' . $user->intUser_ID . '"><i class="fas fa-trash-alt"></i></a>';
                $resetPassBtn = '';
                // Tampilkan tombol reset hanya jika user yang login adalah 'akademik'
                if (auth()->user()->hasRole('akademik')) {
                    $resetPassBtn = '<a href="javascript:void(0)" class="btn btn-secondary btn-sm reset-password-btn"
                                    data-id="' . $user->intUser_ID . '"
                                    data-name="' . e($user->txtFullName) . '">
                                    <i class="fas fa-key"></i>
                                </a>';
                }
                return '<div class="d-flex gap-2">' . $viewBtn . $editBtn . $deleteBtn . $resetPassBtn . '</div>';
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
                    'intConcentrate_ID' => 'nullable|exists:concentrates,intConcentrate_ID',
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

            if ($request->role == 'dosen' || $request->role == 'kaprodi') {
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
        $user->load('roles','mahasiswaProfile.major','mahasiswaProfile.concentrate', 'dosenProfile.major');
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
                    'intConcentrate_ID' => 'nullable|exists:concentrates,intConcentrate_ID',
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
            if ($request->role == 'dosen' || $request->role == 'kaprodi') {
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

    public function profile()
    {
        $user = Auth::user()->load('roles', 'mahasiswaProfile.major', 'mahasiswaProfile.concentrate', 'dosenProfile.major');
        $majors = Major::all();
        return view('auth.profile', compact('user', 'majors'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        // Aturan validasi dasar
        $rules = [
            'txtFullName'   => 'required|string|max:255',
            'txtBirthPlace' => 'required|string|max:255',
            'dtmBirthDate'  => 'required|date',
            'txtGender'     => 'required|in:L,P',
        ];

        // Aturan validasi kondisional untuk Mahasiswa
        if ($user->hasRole('mahasiswa')) {
            $rules['txtNIM'] = ['required', 'string', Rule::unique('mahasiswa_profiles')->ignore($user->mahasiswaProfile->intMahasiswaProfile_ID ?? null, 'intMahasiswaProfile_ID')];
            $rules['txtYear'] = 'nullable|string|max:4';
            $rules['intMajor_ID'] = 'required|exists:majors,intMajor_ID';
            $rules['intConcentrate_ID'] = 'nullable|exists:concentrates,intConcentrate_ID';
        }

        // Aturan validasi kondisional untuk Dosen/Kaprodi
        if ($user->hasRole(['dosen', 'kaprodi'])) {
            $rules['txtNIP'] = ['required', 'string', Rule::unique('dosen_profiles')->ignore($user->dosenProfile->intDosenProfile_ID ?? null, 'intDosenProfile_ID')];
            $rules['txtNIDN'] = ['required', 'string', Rule::unique('dosen_profiles')->ignore($user->dosenProfile->intDosenProfile_ID ?? null, 'intDosenProfile_ID')];
            $rules['intMajor_ID_dosen'] = 'required|exists:majors,intMajor_ID';
        }

        $validatedData = $request->validate($rules);

        DB::beginTransaction();
        try {
            // Update data di tabel users
            $user->update([
                'txtFullName'   => $validatedData['txtFullName'],
                'txtBirthPlace' => $validatedData['txtBirthPlace'],
                'dtmBirthDate'  => $validatedData['dtmBirthDate'],
                'txtGender'     => $validatedData['txtGender'],
            ]);

            // Update profil mahasiswa jika ada
            if ($user->hasRole('mahasiswa') && $user->mahasiswaProfile) {
                $user->mahasiswaProfile->update([
                    'txtNIM'            => $validatedData['txtNIM'],
                    'txtYear'           => $request->txtYear,
                    'intMajor_ID'       => $validatedData['intMajor_ID'],
                    'intConcentrate_ID' => $request->filled('intConcentrate_ID') ? $validatedData['intConcentrate_ID'] : null,
                ]);
            }

            // Update profil dosen jika ada
            if ($user->hasRole(['dosen', 'kaprodi']) && $user->dosenProfile) {
                $user->dosenProfile->update([
                    'txtNIP'              => $validatedData['txtNIP'],
                    'txtNIDN'             => $validatedData['txtNIDN'],
                    'intMajor_ID'         => $request->intMajor_ID_dosen,
                    'txtFieldOfKnowledge' => $request->txtFieldOfKnowledge,
                ]);
            }

            DB::commit();

            return response()->json(['success' => 'Profil berhasil diperbarui!']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Gagal memperbarui profil: ' . $e->getMessage()], 500);
        }
    }

    public function resetPassword(Request $request, User $user)
    {
        // 1. Validasi input
        $validator = Validator::make($request->all(), [
            'new_password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        try {
            // 2. Update password user
            $user->update([
                'txtPassword' => Hash::make($request->new_password)
            ]);

            // 3. Kirim respons sukses
            return response()->json(['success' => 'Password untuk ' . $user->txtFullName . ' berhasil direset.']);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal mereset password: ' . $e->getMessage()], 500);
        }
    }

    public function import(Request $request)
    {
        $request->validate([
            'import_file' => 'required|mimes:xlsx,xls',
        ]);

        try {
            Excel::import(new UsersImport, $request->file('import_file'));
            return redirect()->route('users.index')->with('success', 'Data user berhasil diimpor!');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
             $failures = $e->failures();
             // Anda bisa memformat pesan error di sini
             return redirect()->route('users.index')->with('error', 'Terjadi error validasi saat impor.');
        } catch (\Exception $e) {
            return redirect()->route('users.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        $headings = [
            'nama_lengkap', 'email', 'password', 'tempat_lahir', 'tanggal_lahir',
            'jenis_kelamin_l_p', 'telepon', 'role',
            // Mahasiswa fields
            'nim', 'tahun_angkatan', 'id_prodi', 'id_peminatan',
            // Dosen fields
            'nip', 'nidn', 'id_prodi_homebase', 'bidang_keilmuan'
        ];

        // Membuat file Excel kosong dengan heading saja
        return Excel::download(new class($headings) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
            private $headings;
            public function __construct($headings) { $this->headings = $headings; }
            public function collection() { return collect([]); }
            public function headings(): array { return $this->headings; }
        }, 'template_import_users.xlsx');
    }

    public function importMahasiswa(Request $request)
    {
        $request->validate(['import_file' => 'required|mimes:xlsx,xls']);

        try {
            Excel::import(new MahasiswaImport, $request->file('import_file'));
            return redirect()->route('users.index')->with('success', 'Data mahasiswa berhasil diimpor!');
        } catch (\Exception $e) {
            return redirect()->route('users.index')->with('error', 'Terjadi kesalahan saat impor: ' . $e->getMessage());
        }
    }

    public function downloadMahasiswaTemplate()
    {
        $headings = [
            'nama_lengkap', 'email', 'password', 'tempat_lahir', 'tanggal_lahir',
            'jenis_kelamin_l_p', 'telepon',
            // Hanya field mahasiswa
            'nim', 'tahun_angkatan', 'id_prodi', 'id_peminatan',
        ];

        return Excel::download(new class($headings) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
            private $headings;
            public function __construct($headings) { $this->headings = $headings; }
            public function collection() { return collect([]); }
            public function headings(): array { return $this->headings; }
        }, 'template_import_mahasiswa.xlsx');
    }

    public function importDosen(Request $request)
    {
        $request->validate(['import_file' => 'required|mimes:xlsx,xls']);

        try {
            Excel::import(new DosenImport, $request->file('import_file'));
            return redirect()->route('users.index')->with('success', 'Data dosen & kaprodi berhasil diimpor!');
        } catch (\Exception $e) {
            return redirect()->route('users.index')->with('error', 'Terjadi kesalahan saat impor: ' . $e->getMessage());
        }
    }

    public function downloadDosenTemplate()
    {
        $headings = [
            'nama_lengkap', 'email', 'password', 'tempat_lahir', 'tanggal_lahir',
            'jenis_kelamin_l_p', 'telepon', 'role', // Role wajib diisi (dosen/kaprodi)
            // Hanya field dosen
            'nip', 'nidn', 'id_prodi_homebase', 'bidang_keilmuan',
        ];

        return Excel::download(new class($headings) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
            private $headings;
            public function __construct($headings) { $this->headings = $headings; }
            public function collection() { return collect([]); }
            public function headings(): array { return $this->headings; }
        }, 'template_import_dosen.xlsx');
    }
}

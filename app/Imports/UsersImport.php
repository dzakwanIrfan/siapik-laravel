<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithTransactions;

class UsersImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Menggunakan DB::transaction di sini, atau WithTransactions di atas
        // untuk memastikan jika profil gagal dibuat, user juga tidak akan dibuat.

        $user = User::create([
            'txtFullName'   => $row['nama_lengkap'],
            'txtEmail'      => $row['email'],
            'txtPassword'   => Hash::make($row['password']),
            'txtBirthPlace' => $row['tempat_lahir'],
            'dtmBirthDate'  => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['tanggal_lahir']),
            'txtGender'     => $row['jenis_kelamin_l_p'],
            'txtPhone'      => $row['telepon'],
        ]);

        // Tetapkan role ke user
        $user->assignRole($row['role']);

        // Jika rolenya adalah mahasiswa, buat profil mahasiswa
        if (strtolower($row['role']) === 'mahasiswa') {
            $user->mahasiswaProfile()->create([
                'txtNIM'        => $row['nim'],
                'txtYear'       => $row['tahun_angkatan'],
                'intMajor_ID'   => $row['id_prodi'],
                'intConcentrate_ID' => $row['id_peminatan'] ?? null,
            ]);
        }

        // Jika rolenya adalah dosen atau kaprodi, buat profil dosen
        if (in_array(strtolower($row['role']), ['dosen', 'kaprodi'])) {
            $user->dosenProfile()->create([
                'txtNIP'      => $row['nip'],
                'txtNIDN'     => $row['nidn'],
                'intMajor_ID' => $row['id_prodi_homebase'],
                'txtFieldOfKnowledge' => $row['bidang_keilmuan'] ?? null,
            ]);
        }

        return $user;
    }
}

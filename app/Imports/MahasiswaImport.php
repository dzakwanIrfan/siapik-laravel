<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MahasiswaImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // 4. Bungkus semua logika dalam DB::transaction()
        return DB::transaction(function() use ($row) {
            $user = User::create([
                'txtFullName'   => $row['nama_lengkap'],
                'txtEmail'      => $row['email'],
                'txtPassword'   => Hash::make($row['password']),
                'txtBirthPlace' => $row['tempat_lahir'],
                'dtmBirthDate'  => $row['tanggal_lahir'],
                'txtGender'     => $row['jenis_kelamin_l_p'],
                'txtPhone'      => $row['telepon'],
            ]);

            $user->assignRole('mahasiswa');

            $user->mahasiswaProfile()->create([
                'txtNIM'        => $row['nim'],
                'txtYear'       => $row['tahun_angkatan'],
                'intMajor_ID'   => $row['id_prodi'],
                'intConcentrate_ID' => $row['id_peminatan'] ?? null,
            ]);

            return $user;
        });
    }
}

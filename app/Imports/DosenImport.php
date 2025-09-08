<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;

class DosenImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return DB::transaction(function() use ($row) {
            $user = User::create([
                'txtFullName'   => $row['nama_lengkap'],
                'txtEmail'      => $row['email'],
                'txtPassword'   => Hash::make($row['password']),
                'txtBirthPlace' => $row['tempat_lahir'],
                'dtmBirthDate'  => $row['tanggal_lahir'], // Asumsi format YYYY-MM-DD
                'txtGender'     => $row['jenis_kelamin_l_p'],
                'txtPhone'      => $row['telepon'],
            ]);

            // Tetapkan role berdasarkan kolom 'role' di Excel
            // Pastikan nilainya 'dosen' atau 'kaprodi'
            $role = strtolower($row['role']);
            if (in_array($role, ['dosen', 'kaprodi'])) {
                $user->assignRole($role);
            } else {
                // Default ke 'dosen' jika tidak valid untuk keamanan
                $user->assignRole('dosen');
            }

            // Buat profil dosen
            $user->dosenProfile()->create([
                'txtNIP'      => $row['nip'],
                'txtNIDN'     => $row['nidn'],
                'intMajor_ID' => $row['id_prodi_homebase'],
                'txtFieldOfKnowledge' => $row['bidang_keilmuan'] ?? null,
            ]);

            return $user;
        });
    }
}

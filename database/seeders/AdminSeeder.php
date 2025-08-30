<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use App\Models\MahasiswaProfile;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Roles dasar (hapus jika tidak pakai Spatie)
        foreach (['mahasiswa', 'kaprodi', 'dosen', 'akademik', 'admin'] as $r) {
            Role::firstOrCreate(['name' => $r, 'guard_name' => 'web']);
        }

        $admin = User::firstOrCreate(
            ['txtEmail' => 'admin@kampus.ac.id'],
            [
                'txtFullName'  => 'Administrator',
                'txtPassword'  => 'admin123', // akan ter-hash oleh cast
                'txtGender'    => 'L',
                'txtBirthPlace'=> 'Purwokerto',
                'dtmBirthDate' => now()->subYears(30),
                'bitActive'    => 1,
            ]
        );

        $mahasiswa = User::firstOrCreate(
            ['txtEmail' => 'mahasiswa@kampus.ac.id'],
            [
                'txtFullName'  => 'Ghaza Indra',
                'txtPassword'  => 'mahasiswa123', // akan ter-hash oleh cast
                'txtGender'    => 'L',
                'txtBirthPlace'=> 'Purwokerto',
                'dtmBirthDate' => now()->subYears(20),
                'bitActive'    => 1,
            ]
        );

        MahasiswaProfile::firstOrCreate(
            ['intUser_ID' => $mahasiswa->intUser_ID],
            [
                'txtNIM' => 'H1D022073',
                'intMajor_ID' => 1, // Ilmu Pertanian (S3)
                'intConcentrate_ID' => 1, // Agronomi
                'txtYear' => '2023',
                'bitActive' => 1,
            ]
        );

        // Assign role admin (hapus jika tidak pakai Spatie)
        if (method_exists($admin, 'assignRole')) {
            $admin->assignRole('admin');
        }

        if (method_exists($mahasiswa, 'assignRole')) {
            $mahasiswa->assignRole('mahasiswa');
        }
    }
}

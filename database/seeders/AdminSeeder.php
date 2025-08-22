<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Roles dasar (hapus jika tidak pakai Spatie)
        foreach (['mahasiswa', 'kaprodi', 'akademik', 'admin'] as $r) {
            Role::firstOrCreate(['name' => $r, 'guard_name' => 'web']);
        }

        $admin = User::firstOrCreate(
            ['txtEmail' => 'admin@kampus.ac.id'],
            [
                'txtFullName'  => 'Administrator',
                'txtPassword'  => 'admin123', // akan ter-hash oleh cast
                'txtNim'       => '0000000000',
                'txtGender'    => 'L',
                'txtBirthPlace'=> 'Purwokerto',
                'dtmBirthDate' => now()->subYears(30),
                'bitActive'    => 1,
            ]
        );

        // Assign role admin (hapus jika tidak pakai Spatie)
        if (method_exists($admin, 'assignRole')) {
            $admin->assignRole('admin');
        }
    }
}

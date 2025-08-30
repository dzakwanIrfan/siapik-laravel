<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use App\Models\MahasiswaProfile;
use App\Models\DosenProfile;
use Spatie\Permission\Models\Role;
use Faker\Factory as Faker;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Inisialisasi Faker untuk format Indonesia
        $faker = Faker::create('id_ID');

        // 1. Buat Roles (tanpa admin)
        $roles = ['mahasiswa', 'kaprodi', 'dosen', 'akademik'];
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }

        // 2. Buat 2 user Mahasiswa beserta profilnya
        for ($i = 0; $i < 2; $i++) {
            $fullName = $faker->name();
            $mahasiswa = User::firstOrCreate(
                ['txtEmail' => $faker->unique()->safeEmail()],
                [
                    'txtFullName'   => $fullName,
                    'txtPassword'   => 'password', // akan ter-hash oleh cast
                    'txtGender'     => $faker->randomElement(['L', 'P']),
                    'txtBirthPlace' => $faker->city(),
                    'dtmBirthDate'  => $faker->dateTimeBetween('-22 years', '-19 years'),
                    'bitActive'     => 1,
                ]
            );
            $mahasiswa->assignRole('mahasiswa');

            MahasiswaProfile::firstOrCreate(
                ['intUser_ID' => $mahasiswa->intUser_ID],
                [
                    'txtNIM'            => 'H1D022' . $faker->unique()->numerify('###'),
                    'intMajor_ID'       => 1, // Sesuaikan ID Prodi
                    'intConcentrate_ID' => 1, // Sesuaikan ID Peminatan
                    'txtYear'           => '2022',
                    'bitActive'         => 1,
                ]
            );
        }

        // 3. Buat 2 user Dosen beserta profilnya
        for ($i = 0; $i < 2; $i++) {
            $fullName = $faker->name();
            $dosen = User::firstOrCreate(
                ['txtEmail' => $faker->unique()->safeEmail()],
                [
                    'txtFullName'   => $fullName,
                    'txtPassword'   => 'password',
                    'txtGender'     => $faker->randomElement(['L', 'P']),
                    'txtBirthPlace' => $faker->city(),
                    'dtmBirthDate'  => $faker->dateTimeBetween('-45 years', '-30 years'),
                    'bitActive'     => 1,
                ]
            );
            $dosen->assignRole('dosen');

            DosenProfile::firstOrCreate(
                ['intUser_ID' => $dosen->intUser_ID],
                [
                    'txtNIP'              => $faker->unique()->numerify('198##########'),
                    'txtNIDN'             => $faker->unique()->numerify('00########'),
                    'intMajor_ID'         => 2, // Sesuaikan ID Prodi
                    'txtFieldOfKnowledge' => 'Ilmu Komputer',
                    'bitActive'           => 1,
                ]
            );
        }

        // 4. Buat 2 user Kaprodi
        for ($i = 0; $i < 2; $i++) {
            $fullName = $faker->name();
            $kaprodi = User::firstOrCreate(
                ['txtEmail' => $faker->unique()->safeEmail()],
                [
                    'txtFullName'   => $fullName,
                    'txtPassword'   => 'password',
                    'txtGender'     => $faker->randomElement(['L', 'P']),
                    'txtBirthPlace' => $faker->city(),
                    'dtmBirthDate'  => $faker->dateTimeBetween('-50 years', '-40 years'),
                    'bitActive'     => 1,
                ]
            );
            $kaprodi->assignRole('kaprodi');
        }

        // 5. Buat 2 user Akademik
        for ($i = 0; $i < 2; $i++) {
            $fullName = $faker->name();
            $akademik = User::firstOrCreate(
                ['txtEmail' => $faker->unique()->safeEmail()],
                [
                    'txtFullName'   => $fullName,
                    'txtPassword'   => 'password',
                    'txtGender'     => $faker->randomElement(['L', 'P']),
                    'txtBirthPlace' => $faker->city(),
                    'dtmBirthDate'  => $faker->dateTimeBetween('-30 years', '-25 years'),
                    'bitActive'     => 1,
                ]
            );
            $akademik->assignRole('akademik');
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

         if (method_exists($admin, 'assignRole')) {
            $admin->assignRole('akademik');
        }

    }
}
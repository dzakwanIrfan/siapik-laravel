<?php

namespace Database\Seeders;

use App\Models\Major;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MajorsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $majors = [
            [
                'txtNameMajor' => 'Ilmu Pertanian (S3)',
                'txtStrata' => 'S3',
                'txtTitle' => 'Doktor',
                'txtShortTitle' => 'Dr.',
            ],
            [
                'txtNameMajor' => 'Ilmu Manajemen (S3)',
                'txtStrata' => 'S3',
                'txtTitle' => 'Doktor',
                'txtShortTitle' => 'Dr.',
            ],
            [
                'txtNameMajor' => 'Ilmu Ekonomi (S3)',
                'txtStrata' => 'S3',
                'txtTitle' => 'Doktor',
                'txtShortTitle' => 'Dr.',
            ],
            [
                'txtNameMajor' => 'Perencanaan dan Pengembangan Wilayah',
                'txtStrata' => 'S2',
                'txtTitle' => 'Master Perencanaan Wilayah',
                'txtShortTitle' => 'M.P.W.K',
            ],
            [
                'txtNameMajor' => 'Hukum',
                'txtStrata' => 'S2',
                'txtTitle' => 'Magister Hukum',
                'txtShortTitle' => 'M.H',
            ],
            [
                'txtNameMajor' => 'Administrasi Publik',
                'txtStrata' => 'S2',
                'txtTitle' => 'Magister Administrasi Publik',
                'txtShortTitle' => 'M.A.P',
            ],
            [
                'txtNameMajor' => 'Geografi',
                'txtStrata' => 'S2',
                'txtTitle' => 'Magister Geografi',
                'txtShortTitle' => 'M. Geo',
            ],
            [
                'txtNameMajor' => 'Kajian Budaya',
                'txtStrata' => 'S2',
                'txtTitle' => 'Magister Sosial',
                'txtShortTitle' => 'M.Sos',
            ],
            [
                'txtNameMajor' => 'Agronomi',
                'txtStrata' => 'S2',
                'txtTitle' => 'Magister Pertanian',
                'txtShortTitle' => 'M.P',
            ],
            [
                'txtNameMajor' => 'Pendidikan Matematika',
                'txtStrata' => 'S2',
                'txtTitle' => 'Magister Pendidikan',
                'txtShortTitle' => 'M.Pd',
            ],
            [
                'txtNameMajor' => 'Pendidikan IPA',
                'txtStrata' => 'S2',
                'txtTitle' => 'Magister Pendidikan',
                'txtShortTitle' => 'M. Pd',
            ],
            [
                'txtNameMajor' => 'Manajemen Rekayasa',
                'txtStrata' => 'S2',
                'txtTitle' => 'Magister Teknik',
                'txtShortTitle' => 'M.T',
            ],
            [
                'txtNameMajor' => 'Peternakan',
                'txtStrata' => 'S2',
                'txtTitle' => 'Magister Peternakan',
                'txtShortTitle' => 'M.Pt.',
            ],
            [
                'txtNameMajor' => 'Pendidikan IPS',
                'txtStrata' => 'S2',
                'txtTitle' => 'Magister Pendidikan',
                'txtShortTitle' => 'M.Pd',
            ],
            [
                'txtNameMajor' => 'Ilmu Ekonomi',
                'txtStrata' => 'S2',
                'txtTitle' => 'Magister Ekonomi',
                'txtShortTitle' => 'M.E',
            ],
            [
                'txtNameMajor' => 'Fisika',
                'txtStrata' => 'S2',
                'txtTitle' => 'Magister Sains',
                'txtShortTitle' => 'M.Si',
            ],
            [
                'txtNameMajor' => 'Kesehatan Masyarakat',
                'txtStrata' => 'S2',
                'txtTitle' => 'Magister Kesehatan Masyarakat',
                'txtShortTitle' => 'M.Kes',
            ],
            [
                'txtNameMajor' => 'Ilmu Perikanan',
                'txtStrata' => 'S2',
                'txtTitle' => 'Magister Ilmu Perikanan',
                'txtShortTitle' => 'M.Pi',
            ],
            [
                'txtNameMajor' => 'Pendidikan Bahasa dan Sastra Indonesia',
                'txtStrata' => 'S2',
                'txtTitle' => 'Magister Pendidikan',
                'txtShortTitle' => 'M.Pd',
            ],
            [
                'txtNameMajor' => 'Pendidikan Seni',
                'txtStrata' => 'S2',
                'txtTitle' => 'Magister Pendidikan Seni',
                'txtShortTitle' => 'M.Pd',
            ],
            [
                'txtNameMajor' => 'Agribisnis',
                'txtStrata' => 'S2',
                'txtTitle' => 'Magister Pertanian',
                'txtShortTitle' => 'M.P',
            ],
            [
                'txtNameMajor' => 'Keguruan Bahasa',
                'txtStrata' => 'S2',
                'txtTitle' => 'Magister Pendidikan',
                'txtShortTitle' => 'M.Pd',
            ],
            [
                'txtNameMajor' => 'Kimia',
                'txtStrata' => 'S2',
                'txtTitle' => 'Magister Sains',
                'txtShortTitle' => 'M.Si',
            ],
            [
                'txtNameMajor' => 'Ilmu Manajemen',
                'txtStrata' => 'S2',
                'txtTitle' => 'Magister Manajemen',
                'txtShortTitle' => 'M.M',
            ]
        ];

        foreach ($majors as $major) {
            Major::firstOrCreate($major);
        }
    }
}

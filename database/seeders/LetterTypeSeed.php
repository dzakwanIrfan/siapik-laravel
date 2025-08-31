<?php

namespace Database\Seeders;

use App\Models\LetterType;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class LetterTypeSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $letter_types = [
            [
                'txtNameLetterType' => 'Surat Izin Penelitian',
                'txtCode' => 'SIP',
                'txtDescription' => 'Pengajuan Surat Izin Penelitian bagi Mahasiswa Pascasarjana Universitas Halu Oleo',
                'txtTemplatePath' => 'templates.surat_izin_penelitian',
                'bitActive' => 1,
                'txtInsertedBy' => 'Seeder',
                'txtInserted' => now(),
                'txtUpdatedBy' => 'Seeder',
                'txtUpdated' => now(),
            ],
            [
                'txtNameLetterType' => 'Surat Keterangan Aktif Kuliah',
                'txtCode' => 'SKAK',
                'txtDescription' => 'Pengajuan Surat Keterangan Aktif Kuliah bagi Mahasiswa Universitas Halu Oleo',
                'txtTemplatePath' => 'path/to/template_b',
                'bitActive' => 1,
                'txtInsertedBy' => 'Seeder',
                'txtInserted' => now(),
                'txtUpdatedBy' => 'Seeder',
                'txtUpdated' => now(),
            ],
            [
                'txtNameLetterType' => 'Surat Keterangan Alumni',
                'txtCode' => 'SKAL',
                'txtDescription' => 'Pengajuan Surat Permohonan Keterangan Alumni Pascasarjana Universitas Halu Oleo',
                'txtTemplatePath' => 'path/to/template_c',
                'bitActive' => 1,
                'txtInsertedBy' => 'Seeder',
                'txtInserted' => now(),
                'txtUpdatedBy' => 'Seeder',
                'txtUpdated' => now(),
            ],
            [
                'txtNameLetterType' => 'Surat Keterangan Lulus',
                'txtCode' => 'SKL',
                'txtDescription' => 'Pengajuan Surat Keterangan Lulus bagi Mahasiswa Pascasarjana Universitas Halu Oleo',
                'txtTemplatePath' => 'path/to/template_d',
                'bitActive' => 1,
                'txtInsertedBy' => 'Seeder',
                'txtInserted' => now(),
                'txtUpdatedBy' => 'Seeder',
                'txtUpdated' => now(),
            ],
            [
                'txtNameLetterType' => 'Surat Pengantar TOEFL',
                'txtCode' => 'SPT',
                'txtDescription' => 'Pengajuan Surat Pengantar TOEFL Mahasiswa Pascasarjana Universitas Halu Oleo',
                'txtTemplatePath' => 'path/to/template_e',
                'bitActive' => 1,
                'txtInsertedBy' => 'Seeder',
                'txtInserted' => now(),
                'txtUpdatedBy' => 'Seeder',
                'txtUpdated' => now(),
            ],
            [
                'txtNameLetterType' => 'Surat Keterangan Tidak Menerima Beasiswa',
                'txtCode' => 'SKTMB',
                'txtDescription' => 'Pengajuan Surat Keterangan Tidak Sedang Menerima Beasiswa dari Sumber Lain',
                'txtTemplatePath' => 'path/to/template_f',
                'bitActive' => 1,
                'txtInsertedBy' => 'Seeder',
                'txtInserted' => now(),
                'txtUpdatedBy' => 'Seeder',
                'txtUpdated' => now(),
            ],
        ];

        foreach ($letter_types as $type) {
            LetterType::create($type);
        }
    }
}

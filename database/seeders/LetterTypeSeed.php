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
                'bitUjian' => 0,
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
                'bitUjian' => 0,
                'txtNameLetterType' => 'Surat Keterangan Aktif Kuliah',
                'txtCode' => 'SKAK',
                'txtDescription' => 'Pengajuan Surat Keterangan Aktif Kuliah bagi Mahasiswa Universitas Halu Oleo',
                'txtTemplatePath' => 'templates.surat_keterangan_aktif_kuliah',
                'bitActive' => 1,
                'txtInsertedBy' => 'Seeder',
                'txtInserted' => now(),
                'txtUpdatedBy' => 'Seeder',
                'txtUpdated' => now(),
            ],
            [
                'bitUjian' => 0,
                'txtNameLetterType' => 'Surat Keterangan Alumni',
                'txtCode' => 'SKAL',
                'txtDescription' => 'Pengajuan Surat Permohonan Keterangan Alumni Pascasarjana Universitas Halu Oleo',
                'txtTemplatePath' => 'templates.surat_keterangan_alumni',
                'bitActive' => 1,
                'txtInsertedBy' => 'Seeder',
                'txtInserted' => now(),
                'txtUpdatedBy' => 'Seeder',
                'txtUpdated' => now(),
            ],
            [
                'bitUjian' => 0,
                'txtNameLetterType' => 'Surat Keterangan Lulus',
                'txtCode' => 'SKL',
                'txtDescription' => 'Pengajuan Surat Keterangan Lulus bagi Mahasiswa Pascasarjana Universitas Halu Oleo',
                'txtTemplatePath' => 'templates.surat_keterangan_lulus',
                'bitActive' => 1,
                'txtInsertedBy' => 'Seeder',
                'txtInserted' => now(),
                'txtUpdatedBy' => 'Seeder',
                'txtUpdated' => now(),
            ],
            [
                'bitUjian' => 0,
                'txtNameLetterType' => 'Surat Pengantar TOEFL',
                'txtCode' => 'SPT',
                'txtDescription' => 'Pengajuan Surat Pengantar TOEFL Mahasiswa Pascasarjana Universitas Halu Oleo',
                'txtTemplatePath' => 'templates.surat_pengantar_toefl',
                'bitActive' => 1,
                'txtInsertedBy' => 'Seeder',
                'txtInserted' => now(),
                'txtUpdatedBy' => 'Seeder',
                'txtUpdated' => now(),
            ],
            [
                'bitUjian' => 0,
                'txtNameLetterType' => 'Surat Keterangan Tidak Menerima Beasiswa',
                'txtCode' => 'SKTMB',
                'txtDescription' => 'Pengajuan Surat Keterangan Tidak Sedang Menerima Beasiswa dari Sumber Lain',
                'txtTemplatePath' => 'templates.surat_tidak_terima_beasiswa',
                'bitActive' => 1,
                'txtInsertedBy' => 'Seeder',
                'txtInserted' => now(),
                'txtUpdatedBy' => 'Seeder',
                'txtUpdated' => now(),
            ],
            [
                'bitUjian' => 0,
                'txtNameLetterType' => 'Surat Pengembalian ke Instansi',
                'txtCode' => 'SPKI',
                'txtDescription' => 'Pengajuan Surat Pengembalian ke Instansi bagi Mahasiswa Pascasarjana Universitas Halu Oleo yang telah menyesaiakan Studi',
                'txtTemplatePath' => 'templates.surat_pengembalian_ke_instansi',
                'bitActive' => 1,
                'txtInsertedBy' => 'Seeder',
                'txtInserted' => now(),
                'txtUpdatedBy' => 'Seeder',
                'txtUpdated' => now(),
            ],
            [
                'bitUjian' => 1,
                'txtNameLetterType' => 'Usulan Ujian / Seminar Magister (S2)',
                'txtCode' => 'UU2',
                'txtDescription' => 'Pengajuan Surat Usulan Ujian / Seminar Magister (S2)',
                'txtTemplatePath' => 'templates.surat_tugas_pembimbing_ujian_s2',
                'bitActive' => 1,
                'txtInsertedBy' => 'Seeder',
                'txtInserted' => now(),
                'txtUpdatedBy' => 'Seeder',
                'txtUpdated' => now(),
            ],
            [
                'bitUjian' => 1,
                'txtNameLetterType' => 'Usulan Ujian / Seminar Doktor (S3)',
                'txtCode' => 'UU3',
                'txtDescription' => 'Pengajuan Surat Usulan Ujian / Seminar Doktor (S3)',
                'txtTemplatePath' => 'templates.surat_tugas_pembimbing_ujian_s3',
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

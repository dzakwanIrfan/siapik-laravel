<?php

namespace Database\Seeders;

use App\Models\LetterField;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class LetterFieldSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $letter_fields = [
            [
                'intLetterType_ID' => 1,
                'txtFieldName' => 'txtTujuanSurat',
                'txtFieldLabel' => 'Tujuan Surat',
                'txtFieldType' => 'text',
                'jsonFieldOptions' => null,
                'bitRequired' => 1,
                'intFieldOrder' => 1,
                'jsonFieldValidation' => '{"required": true}',
                'bitActive' => 1,
                'txtInsertedBy' => 'Seeder',
                'txtInserted' => now(),
                'txtUpdatedBy' => 'Seeder',
                'txtUpdated' => now(),
            ],
            [
                'intLetterType_ID' => 1,
                'txtFieldName' => 'txtJudulTesis',
                'txtFieldLabel' => 'Judul Tesis',
                'txtFieldType' => 'textarea',
                'jsonFieldOptions' => null,
                'bitRequired' => 1,
                'intFieldOrder' => 2,
                'jsonFieldValidation' => '{"required": true}',
                'bitActive' => 1,
                'txtInsertedBy' => 'Seeder',
                'txtInserted' => now(),
                'txtUpdatedBy' => 'Seeder',
                'txtUpdated' => now(),
            ],
            [
                'intLetterType_ID' => 1,
                'txtFieldName' => 'fileBuktiPembayaranSPP',
                'txtFieldLabel' => 'Bukti Pembayaran SPP Semester yang sedang berjalan',
                'txtFieldType' => 'file',
                'jsonFieldOptions' => null,
                'bitRequired' => 1,
                'intFieldOrder' => 3,
                'jsonFieldValidation' => '{"required": true}',
                'bitActive' => 1,
                'txtInsertedBy' => 'Seeder',
                'txtInserted' => now(),
                'txtUpdatedBy' => 'Seeder',
                'txtUpdated' => now(),
            ],
            [
                'intLetterType_ID' => 1,
                'txtFieldName' => 'fileHalamanPengesahan',
                'txtFieldLabel' => 'Halaman pengesahan yang ditanda tangan Ketua Program Studi ( acc penelitian )',
                'txtFieldType' => 'file',
                'jsonFieldOptions' => null,
                'bitRequired' => 1,
                'intFieldOrder' => 4,
                'jsonFieldValidation' => '{"required": true}',
                'bitActive' => 1,
                'txtInsertedBy' => 'Seeder',
                'txtInserted' => now(),
                'txtUpdatedBy' => 'Seeder',
                'txtUpdated' => now(),
            ],
            [
                'intLetterType_ID' => 1,
                'txtFieldName' => 'fileIzinPenelitian',
                'txtFieldLabel' => 'Format ijin penelitian dari program studi',
                'txtFieldType' => 'file',
                'jsonFieldOptions' => null,
                'bitRequired' => 1,
                'intFieldOrder' => 5,
                'jsonFieldValidation' => '{"required": true}',
                'bitActive' => 1,
                'txtInsertedBy' => 'Seeder',
                'txtInserted' => now(),
                'txtUpdatedBy' => 'Seeder',
                'txtUpdated' => now(),
            ],
            [
                'intLetterType_ID' => 1,
                'txtFieldName' => 'filePasFoto',
                'txtFieldLabel' => 'Foto 3x4 terbaru',
                'txtFieldType' => 'file',
                'jsonFieldOptions' => null,
                'bitRequired' => 1,
                'intFieldOrder' => 6,
                'jsonFieldValidation' => '{"required": true}',
                'bitActive' => 1,
                'txtInsertedBy' => 'Seeder',
                'txtInserted' => now(),
                'txtUpdatedBy' => 'Seeder',
                'txtUpdated' => now(),
            ]
        ];

        foreach ($letter_fields as $field) {
            LetterField::create($field);
        }
    }
}

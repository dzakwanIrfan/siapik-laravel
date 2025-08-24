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
                'txtTemplatePath' => 'path/to/template_a',
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

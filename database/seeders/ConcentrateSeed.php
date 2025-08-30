<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ConcentrateSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = 'Seeder';
        $now = date('Y-m-d H:i:s');
        
        $concentrates = [
            [
                'intMajor_ID' => 1, // Ilmu Pertanian (S3)
                'txtNameConcentrate' => 'Agronomi',
                'txtInsertedBy' => $user,
                'dtmInserted' => $now,
            ],
            [
                'intMajor_ID' => 1, // Ilmu Pertanian (S3)
                'txtNameConcentrate' => 'Ilmu Tanah',
                'txtInsertedBy' => $user,
                'dtmInserted' => $now,
            ],
            [
                'intMajor_ID' => 1, // Ilmu Pertanian (S3)
                'txtNameConcentrate' => 'Hama',
                'txtInsertedBy' => $user,
                'dtmInserted' => $now,
            ],
        ];

        DB::table('concentrates')->insert($concentrates);

    }
}

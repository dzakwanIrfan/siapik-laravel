<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Database\Seeders\AdminSeeder;
use Database\Seeders\LetterTypeSeed;
use Database\Seeders\ConcentrateSeed;
use Database\Seeders\LetterFieldSeed;
use Database\Seeders\MajorsTableSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            MajorsTableSeeder::class,
            ConcentrateSeed::class,
            LetterTypeSeed::class,
            LetterFieldSeed::class,
            AdminSeeder::class,
        ]);
    }
}

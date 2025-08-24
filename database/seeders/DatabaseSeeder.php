<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Database\Seeders\AdminSeeder;
use Database\Seeders\LetterTypeSeed;
use Database\Seeders\LetterFieldSeed;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            LetterTypeSeed::class,
            LetterFieldSeed::class,
        ]);
    }
}

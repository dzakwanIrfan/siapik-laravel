<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mahasiswa_profiles', function (Blueprint $table) {
            $table->id('intMahasiswaProfile_ID');
            $table->foreignId('intUser_ID')->constrained('users', 'intUser_ID')->onDelete('cascade');
            $table->string('txtNIM')->unique();
            $table->string('txtYear')->nullable();
            $table->foreignId('intMajor_ID')->constrained('majors', 'intMajor_ID')->onDelete('cascade');
            $table->foreignId('intConcentrate_ID')->nullable()->constrained('concentrates', 'intConcentrate_ID')->onDelete('cascade');
            $table->string('txtInsertedBy')->nullable();
            $table->datetime('dtmInserted')->nullable();
            $table->string('txtUpdatedBy')->nullable();
            $table->datetime('dtmUpdated')->nullable();
            $table->tinyInteger('bitActive')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mahasiswa_profiles');
    }
};

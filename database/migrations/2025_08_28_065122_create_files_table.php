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
        Schema::create('files', function (Blueprint $table) {
            $table->id('intFile_ID');
            $table->string('txtFileName');
            $table->string('txtOriginalFileName');
            $table->string('txtFilePath');
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
        Schema::dropIfExists('files');
    }
};

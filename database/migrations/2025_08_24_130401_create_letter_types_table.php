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
        Schema::create('letter_types', function (Blueprint $table) {
            $table->id('intLetterType_ID');
            $table->string('txtNameLetterType');
            $table->string('txtCode')->unique();
            $table->string('txtDescription');
            $table->string('txtTemplatePath');
            $table->tinyInteger('bitActive');
            $table->string('txtInsertedBy');
            $table->datetime('txtInserted');
            $table->string('txtUpdatedBy')->nullable();
            $table->datetime('txtUpdated')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('letter_types');
    }
};

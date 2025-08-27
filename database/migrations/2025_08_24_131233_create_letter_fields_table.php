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
        Schema::create('letter_fields', function (Blueprint $table) {
            $table->id('intLetterField_ID');
            $table->foreignId('intLetterType_ID')
                    ->references('intLetterType_ID')
                    ->on('letter_types')
                    ->cascadeOnDelete()
                    ->cascadeOnUpdate();
            $table->string('txtFieldName');
            $table->string('txtFieldLabel');
            $table->enum('txtFieldType', ['text', 'textarea', 'select', 'date', 'file', 'number']);
            $table->json('jsonFieldOptions')->nullable();
            $table->tinyInteger('bitRequired');
            $table->integer('intFieldOrder');
            $table->json('jsonFieldValidation');
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
        if (Schema::hasTable('letter_fields')) {
            Schema::table('letter_fields', function (Blueprint $table) {
                $table->dropForeign(['intLetterType_ID']);
            });
        }
        Schema::dropIfExists('letter_fields');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('submission_values', function (Blueprint $table) {
            $table->id('intSubmissionValue_ID');

            $table->foreignId('intSubmission_ID')
                  ->constrained('submissions', 'intSubmission_ID')
                  ->cascadeOnUpdate()->cascadeOnDelete();

            $table->foreignId('intLetterField_ID')
                  ->constrained('letter_fields', 'intLetterField_ID')
                  ->cascadeOnUpdate()->restrictOnDelete();

            // Denormalized snapshot agar robust jika definisi field berubah
            $table->string('txtFieldName');
            $table->string('txtFieldLabel');
            $table->string('txtFieldType');

            // Nilai untuk text/number/date/select; untuk file diisi path relatif
            $table->longText('txtFieldValue')->nullable();

            // Metadata (mis. file: original_name, mime, size, disk)
            $table->json('jsonFieldMeta')->nullable();

            $table->tinyInteger('bitActive')->default(1);
            $table->string('txtInsertedBy')->nullable();
            $table->dateTime('dtmInserted')->nullable();
            $table->string('txtUpdatedBy')->nullable();
            $table->dateTime('dtmUpdated')->nullable();

            $table->timestamps();

            // satu field per submission unik
            $table->unique(['intSubmission_ID', 'intLetterField_ID'], 'uq_submission_field');
        });
    }

    public function down(): void
    {
        if (Schema::hasTable('submission_values')) {
            Schema::table('submission_values', function (Blueprint $table) {
                $table->dropForeign(['intSubmission_ID']);
                $table->dropForeign(['intLetterField_ID']);
            });
        }
        Schema::dropIfExists('submission_values');
    }
};

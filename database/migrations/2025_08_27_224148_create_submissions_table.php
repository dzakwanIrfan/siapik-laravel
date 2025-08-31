<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('submissions', function (Blueprint $table) {
            $table->id('intSubmission_ID');
            $table->foreignId('intLetterType_ID')
                  ->constrained('letter_types', 'intLetterType_ID')
                  ->cascadeOnUpdate()->restrictOnDelete();
                  
            $table->foreignId('intUser_ID')
                ->constrained('users', 'intUser_ID')
                ->cascadeOnUpdate()->restrictOnDelete();

            // nomor tanda terima/receipt
            $table->string('txtReceiptNumber')->unique();
            $table->string('txtLetterNumber')->unique()->nullable();

            // status sederhana
            $table->enum('txtStatus', ['Sedang ditinjau Kaprodi', 'Disetujui Kaprodi', 'Ditolak Kaprodi'])->default('Sedang ditinjau Kaprodi');

            $table->tinyInteger('bitActive')->default(1);
            $table->string('txtInsertedBy')->nullable();
            $table->dateTime('dtmInserted')->nullable();
            $table->string('txtUpdatedBy')->nullable();
            $table->dateTime('dtmUpdated')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        if (Schema::hasTable('submissions')) {
            Schema::table('submissions', function (Blueprint $table) {
                $table->dropForeign(['intLetterType_ID']);
                $table->dropForeign(['intUser_ID']);
            });
        }
        Schema::dropIfExists('submissions');
    }
};

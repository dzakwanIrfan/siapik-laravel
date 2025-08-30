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
        Schema::create('submission_statuses', function (Blueprint $table) {
            $table->id('intSubmissionStatus_ID');
            $table->foreignId('intSubmission_ID')->constrained('submissions', 'intSubmission_ID')->onDelete('cascade')->onUpdate('cascade');
            $table->string('txtStatus');
            $table->string('txtInReview');
            $table->tinyInteger('bitActive')->default(1);
            $table->string('txtInsertedBy')->nullable();
            $table->dateTime('dtmInserted')->nullable();
            $table->string('txtUpdatedBy')->nullable();
            $table->dateTime('dtmUpdated')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submission_statuses');
    }
};

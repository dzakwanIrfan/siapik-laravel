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
        Schema::create('submission_chats', function (Blueprint $table) {
            $table->id('intSubmissionChat_ID');
            $table->foreignId('intSubmission_ID')->constrained('submissions', 'intSubmission_ID')->cascadeOnDelete();
            $table->foreignId('intUser_ID')->constrained('users', 'intUser_ID')->cascadeOnDelete();
            $table->text('txtMessage');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submission_chats');
    }
};

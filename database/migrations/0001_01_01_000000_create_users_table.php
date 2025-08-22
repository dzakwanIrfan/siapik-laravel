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
        Schema::create('users', function (Blueprint $table) {
            $table->id('intUser_ID');
            $table->string('txtFullName');
            $table->string('txtEmail')->unique();
            $table->string('txtPassword');
            $table->string('txtNim')->unique();
            $table->enum('txtGender', ['L', 'P']);
            $table->string('txtBirthPlace');
            $table->datetime('dtmBirthDate');
            $table->string('txtYear')->nullable();
            $table->string('txtPhone')->nullable();
            $table->string('txtInsertedBy')->nullable();
            $table->datetime('dtmInserted')->nullable();
            $table->string('txtUpdatedBy')->nullable();
            $table->datetime('dtmUpdated')->nullable();
            $table->tinyInteger('bitActive')->default(1);
            $table->timestamps();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('sessions');
    }
};

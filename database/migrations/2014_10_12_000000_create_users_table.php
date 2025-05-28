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
            $table->bigIncrements('user_id');

            $table->string('username')->unique();
            $table->string('email')->unique()->nullable(); // Nullable for Google login
            $table->string('google_id')->nullable();
            $table->string('password')->nullable(); // Nullable for Google login or judges w/ pin_code

            $table->string('first_name');
            $table->string('last_name');

            $table->enum('role', ['admin', 'tabulator', 'judge']);

            $table->timestamp('email_verified_at')->nullable();
            $table->string('profile_photo')->nullable();
            $table->string('profile_photo_url')->nullable();
            $table->string('profile_photo_public_id')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

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
        Schema::create('events', function (Blueprint $table) {
            $table->id('event_id');
            $table->string('event_name');
            $table->string('venue')->nullable();
            $table->datetime('start_date')->nullable();
            $table->datetime('end_date')->nullable();
            $table->enum('status', ['inactive', 'active', 'completed'])->default('inactive');
            $table->string('cover_photo')->nullable();
            $table->text('description')->nullable();
            $table->enum('division', ['standard', 'male-only', 'female-only'])->default('standard');
            $table->json('statisticians')->nullable();
            $table->foreignId('created_by')->constrained('users', 'user_id')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};

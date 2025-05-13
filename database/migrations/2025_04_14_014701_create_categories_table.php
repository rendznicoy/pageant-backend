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
        Schema::create('categories', function (Blueprint $table) {
            $table->id('category_id');
            $table->foreignId('event_id')->constrained('events', 'event_id')->onDelete('cascade');
            $table->foreignId('stage_id')->constrained('stages', 'stage_id')->onDelete('cascade');
            $table->string('category_name');
            $table->enum('status', ['pending', 'active', 'finalized'])->default('pending');
            $table->unsignedBigInteger('current_candidate_id')->nullable();
            $table->foreign('current_candidate_id')
                  ->references('candidate_id')
                  ->on('candidates')
                  ->onDelete('set null');
            $table->decimal('category_weight', 3, 0); 
            $table->unsignedTinyInteger('max_score')->default(10);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
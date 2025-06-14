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
        Schema::create('scores', function (Blueprint $table) {
            $table->foreignId('judge_id')->constrained('judges', 'judge_id')->onDelete('cascade');
            $table->foreignId('candidate_id')->constrained('candidates', 'candidate_id')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('categories', 'category_id')->onDelete('cascade');
            $table->unsignedBigInteger('stage_id')->nullable();
            $table->foreign('stage_id')
                  ->references('stage_id')
                  ->on('stages')
                  ->onDelete('cascade');
            $table->foreignId('event_id')->constrained('events', 'event_id')->onDelete('cascade');
            $table->unsignedTinyInteger('score')->comment('0 to 100');
            $table->text('comments')->nullable();
            $table->enum('status', ['temporary', 'confirmed'])->default('temporary');
            $table->timestamps();

            $table->primary(['judge_id', 'candidate_id', 'category_id', 'event_id'], 'composite_pk'); // Composite PK
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scores');
    }
};

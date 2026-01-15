<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Felhasználói előrehaladás kérdésekben
        Schema::create('user_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->boolean('is_correct');
            $table->text('user_answer')->nullable();
            $table->integer('time_taken_seconds')->default(0);
            $table->timestamp('created_at')->useCurrent(); // Mikor oldotta meg
        });

        // Kártya előrehaladás
        Schema::create('user_flashcard_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('flashcard_id')->constrained()->onDelete('cascade');
            $table->integer('box')->default(1); // Leitner doboz (1-5)
            $table->timestamp('next_review_at')->useCurrent();
            $table->timestamp('last_reviewed_at')->nullable();
            
            $table->unique(['user_id', 'flashcard_id']);
        });

    }

    public function down(): void
    {
        
        Schema::dropIfExists('user_flashcard_progress');
        Schema::dropIfExists('user_progress');
    }
};
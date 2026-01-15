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

        
    }

    public function down(): void
    {
        
        Schema::dropIfExists('user_progress');
    }
};
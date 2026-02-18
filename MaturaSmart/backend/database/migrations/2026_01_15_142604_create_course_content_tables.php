<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tantárgyak
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name'); 
            $table->string('slug')->unique();
            $table->string('icon')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 2. Unitok (Mappák / Korszakok)
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('order')->default(0); 
            $table->timestamps();
        });

        // 3. Témák (Leckék ÉS Tesztek)
        Schema::create('topics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->foreignId('unit_id')->nullable()->constrained()->onDelete('set null');
            
            $table->string('type')->default('lesson'); 
            
            $table->integer('time_limit_minutes')->nullable(); 
            $table->integer('passing_percentage')->default(50); 
            $table->integer('reading_weight')->default(50); 

            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->longText('content')->nullable(); 
            $table->integer('xp')->default(0);             
            $table->integer('order')->default(0);
            
            $table->integer('year')->nullable();
            $table->string('year_label')->nullable();

            $table->timestamps();
        });

        // 4. Kérdések
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topic_id')->constrained()->onDelete('cascade');
            $table->string('type');
            $table->integer('difficulty')->default(1);
            $table->longText('content'); 
            $table->text('explanation')->nullable();
            $table->string('image_url')->nullable();
            $table->integer('xp')->default(10); 
            $table->timestamps();
        });

        // 5. Válaszlehetőségek
        Schema::create('answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->string('text');
            $table->boolean('is_correct')->default(false);
        });

        // 6. Eredmények
        Schema::create('question_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->boolean('is_correct')->default(false);
            $table->timestamps();
            $table->unique(['user_id', 'question_id']);
        });

        // 7. Flashcard
        Schema::create('flashcards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topic_id')->constrained()->onDelete('cascade');
            $table->text('front'); 
            $table->text('back');  
            $table->string('image_url')->nullable();
            $table->timestamps();
        });

        // 8. Videók
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topic_id')->constrained()->onDelete('cascade');
            $table->string('title')->nullable();
            $table->string('url');
            $table->integer('order')->default(0); 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('videos');
        Schema::dropIfExists('flashcards');
        Schema::dropIfExists('question_user');
        Schema::dropIfExists('answers');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('topics');
        Schema::dropIfExists('units');
        Schema::dropIfExists('subjects');
    }
};
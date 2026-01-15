<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tantárgyak
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique(); // URL-barát név
            $table->string('icon')->nullable();
        });

        // 2. Témakörök
        Schema::create('topics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('order')->default(0); // Sorrendhez
        });

        // 3. KÉRDÉSEK (KVÍZ)
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topic_id')->constrained()->onDelete('cascade');
            $table->string('type');
            $table->integer('difficulty')->default(1);
            $table->longText('content'); // A kérdés szövege (Markdown/HTML)
            $table->text('explanation')->nullable();
            $table->string('image_url')->nullable();
            $table->integer('points')->default(10);
        });

        // 4. Válaszok (feleletválasztós kérdésekhez)
        Schema::create('answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->string('text');
            $table->boolean('is_correct')->default(false);
        });

        // Címkék kérdésekhez
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
        });

        Schema::create('question_tags', function (Blueprint $table) {
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->foreignId('tag_id')->constrained()->onDelete('cascade');
            $table->primary(['question_id', 'tag_id']);
        });

        // Tanulókártyák
        Schema::create('flashcards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topic_id')->constrained()->onDelete('cascade');
            $table->text('front');
            $table->text('back');
            $table->string('image_url')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flashcards');
        Schema::dropIfExists('question_tags');
        Schema::dropIfExists('tags');
        Schema::dropIfExists('answers');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('topics');
        Schema::dropIfExists('subjects');
    }
};
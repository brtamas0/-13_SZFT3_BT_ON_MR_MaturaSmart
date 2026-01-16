<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('topic_completions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->foreignId('topic_id')->constrained()->onDelete('cascade');
        $table->integer('xp_earned')->default(0);
        $table->integer('score_percentage')->nullable();
        $table->timestamps();
        $table->unique(['user_id', 'topic_id']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('topic_completions');
    }
};

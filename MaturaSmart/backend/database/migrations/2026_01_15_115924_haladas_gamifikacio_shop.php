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
        Schema::create('user_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->boolean('is_correct');
            $table->text('user_answer')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->integer('time_taken_seconds')->default(0);
        });

        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->integer('xp_reward')->default(0);
            $table->timestamps();
        });

        Schema::create('user_achievements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('achievement_id')->constrained()->onDelete('cascade');
            $table->timestamp('earned_at')->useCurrent();
        });

        Schema::create('shop_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('cost');
            $table->string('type');
            $table->timestamps();
        });

        Schema::create('user_inventory', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('shop_item_id')->constrained()->onDelete('cascade');
            $table->integer('quantity')->default(1);
            $table->boolean('is_active')->default(false);
            $table->timestamp('purchased_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_inventory');
        Schema::dropIfExists('shop_items');
        Schema::dropIfExists('user_achievements');
        Schema::dropIfExists('achievements');
        Schema::dropIfExists('user_progress');
    }
};

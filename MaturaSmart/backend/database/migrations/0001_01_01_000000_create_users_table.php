<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Felhasználók
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('password')->nullable(); // Nullable a Google login miatt
            $table->string('full_name');
            $table->string('google_id')->nullable()->unique();
            $table->string('avatar_url')->nullable();
            $table->integer('graduation_year')->nullable();
            $table->string('role')->default('student'); // 'student', 'admin', később bővíthető

            // Gamifikáció
            $table->integer('xp')->default(0);
            $table->integer('level')->default(1);
            $table->integer('gems')->default(0);

            //Streak
            $table->timestamp('streak_start')->nullable();
            $table->integer('current_streak')->default(0);
            $table->integer('lost_streak')->default(0);
            $table->timestamp('last_activity')->nullable();

            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
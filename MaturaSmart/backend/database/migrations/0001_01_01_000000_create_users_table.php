<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Felhasználók
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

        // Jelszó visszaállítási tokenek
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // Munkamenetek (Laravel)
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        // Felhasználói beállítások
        Schema::create('user_settings', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('key');
            $table->string('value')->nullable();
            $table->primary(['user_id', 'key']); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_settings');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
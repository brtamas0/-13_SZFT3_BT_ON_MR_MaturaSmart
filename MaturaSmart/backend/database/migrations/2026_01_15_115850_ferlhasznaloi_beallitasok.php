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
        Schema::table('users', function (Blueprint $table) {
            $table->string('google_id')->nullable()->after('email');
            $table->string('avatar_url')->nullable()->after('google_id');
            $table->integer('graduation_year')->nullable()->after('avatar_url');
            $table->enum('role', ['student', 'admin'])->default('student')->after('graduation_year');
            $table->integer('xp')->default(0)->after('role');
            $table->integer('level')->default(1)->after('xp');
            $table->date('streak_start')->nullable()->after('level');
            $table->integer('current_streak')->default(0)->after('streak_start');
            $table->integer('lost_streak')->default(0)->after('current_streak');
            $table->timestamp('last_activity')->nullable()->after('lost_streak');
            $table->integer('gems')->default(0)->after('last_activity');

            $table->renameColumn('password', 'password_hash');
        });

        Schema::create('user_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('key');
            $table->text('value')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};

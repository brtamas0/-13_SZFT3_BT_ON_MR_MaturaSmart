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

        
    }

    public function down(): void
    {
        
        Schema::dropIfExists('topics');
        Schema::dropIfExists('subjects');
    }
};
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
    Schema::table('topics', function (Blueprint $table) {
        $table->integer('xp')->default(50)->after('description');
    });
}

public function down()
{
    Schema::table('topics', function (Blueprint $table) {
        $table->dropColumn('xp');
    });
}
};

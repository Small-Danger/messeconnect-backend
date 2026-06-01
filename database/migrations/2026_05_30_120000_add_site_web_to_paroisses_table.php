<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('paroisses', function (Blueprint $table) {
            $table->string('site_web')->nullable()->after('pays');
        });
    }

    public function down(): void
    {
        Schema::table('paroisses', function (Blueprint $table) {
            $table->dropColumn('site_web');
        });
    }
};

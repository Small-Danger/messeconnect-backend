<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ticket_supports', function (Blueprint $table) {
            $table->text('reponse_admin')->nullable()->after('message');
            $table->timestamp('reponse_admin_at')->nullable()->after('reponse_admin');
        });
    }

    public function down(): void
    {
        Schema::table('ticket_supports', function (Blueprint $table) {
            $table->dropColumn(['reponse_admin', 'reponse_admin_at']);
        });
    }
};

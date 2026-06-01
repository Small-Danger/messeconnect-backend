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
        Schema::create('ticket_supports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('paroisse_id')->constrained('paroisses')->cascadeOnDelete();
            $table->foreignUuid('user_paroisse_id')->nullable()->constrained('user_paroisses')->nullOnDelete();
            $table->string('sujet');
            $table->text('message');
            $table->string('statut')->default('ouvert');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_supports');
    }
};

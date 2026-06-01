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
        Schema::create('favori_paroisses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('fidele_id')->constrained('fideles')->cascadeOnDelete();
            $table->foreignUuid('paroisse_id')->constrained('paroisses')->cascadeOnDelete();
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['fidele_id', 'paroisse_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favori_paroisses');
    }
};

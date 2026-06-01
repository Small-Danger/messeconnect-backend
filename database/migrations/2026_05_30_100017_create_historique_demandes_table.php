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
        Schema::create('historique_demandes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('demande_messe_id')->constrained('demande_messes')->cascadeOnDelete();
            $table->string('statut_precedent')->nullable();
            $table->string('nouveau_statut');
            $table->text('commentaire')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historique_demandes');
    }
};

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
        Schema::create('modele_messes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('paroisse_id')->constrained('paroisses')->cascadeOnDelete();
            $table->string('titre');
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('jour_semaine');
            $table->time('heure');
            $table->boolean('reservable')->default(true);
            $table->unsignedInteger('capacite_max')->nullable();
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modele_messes');
    }
};

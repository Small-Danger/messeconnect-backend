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
        Schema::create('messes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('paroisse_id')->constrained('paroisses')->cascadeOnDelete();
            $table->foreignUuid('modele_messe_id')->nullable()->constrained('modele_messes')->nullOnDelete();
            $table->string('titre');
            $table->text('description')->nullable();
            $table->date('date');
            $table->time('heure');
            $table->boolean('reservable')->default(true);
            $table->unsignedInteger('capacite_max')->nullable();
            $table->unsignedInteger('places_reservees')->default(0);
            $table->boolean('visible')->default(true);
            $table->string('statut')->default('planifiee');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messes');
    }
};

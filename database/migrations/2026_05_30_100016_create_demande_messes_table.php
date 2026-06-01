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
        Schema::create('demande_messes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('fidele_id')->nullable()->constrained('fideles')->nullOnDelete();
            $table->foreignUuid('paroisse_id')->constrained('paroisses')->cascadeOnDelete();
            $table->foreignUuid('messe_id')->constrained('messes')->cascadeOnDelete();
            $table->foreignUuid('type_offrande_id')->constrained('type_offrandes')->cascadeOnDelete();
            $table->string('reference')->unique();
            $table->boolean('est_anonyme')->default(false);
            $table->string('nom_demandeur')->nullable();
            $table->string('email_demandeur')->nullable();
            $table->string('telephone_demandeur')->nullable();
            $table->text('intention')->nullable();
            $table->string('nom_personne_concernee')->nullable();
            $table->decimal('montant', 12, 2);
            $table->string('statut')->default('en_attente');
            $table->timestamps();

            $table->index(['paroisse_id', 'statut']);
            $table->index('fidele_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demande_messes');
    }
};

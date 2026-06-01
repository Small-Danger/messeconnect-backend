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
        Schema::create('paiement_abonnements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('abonnement_id')->constrained('abonnements')->cascadeOnDelete();
            $table->foreignUuid('moyen_paiement_id')->nullable()->constrained('moyen_paiements')->nullOnDelete();
            $table->decimal('montant', 12, 2);
            $table->decimal('frais_techniques', 12, 2)->nullable();
            $table->string('devise', 3)->default('XOF');
            $table->string('reference_interne')->unique();
            $table->string('reference_fournisseur')->nullable()->unique();
            $table->string('status')->default('en_attente');
            $table->string('statut_fournisseur')->nullable();
            $table->string('telephone_payeur')->nullable();
            $table->text('url_checkout')->nullable();
            $table->json('payload_webhook')->nullable();
            $table->dateTime('date_paiement')->nullable();
            $table->dateTime('date_expiration')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paiement_abonnements');
    }
};

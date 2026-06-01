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
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('fidele_id')->constrained('fideles')->cascadeOnDelete();
            $table->foreignUuid('demande_messe_id')->nullable()->constrained('demande_messes')->nullOnDelete();
            $table->string('type');
            $table->string('titre');
            $table->text('contenu')->nullable();
            $table->string('statut')->default('en_attente');
            $table->dateTime('date_envoi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};

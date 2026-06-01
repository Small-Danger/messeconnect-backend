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
        Schema::create('moyen_paiements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('paroisse_id')->constrained('paroisses')->cascadeOnDelete();
            $table->string('type');
            $table->string('environment')->default('sandbox');
            $table->string('numero')->nullable();
            $table->string('identifiant_marchand')->nullable();
            $table->string('client_id')->nullable();
            $table->text('api_key')->nullable();
            $table->text('secret_key')->nullable();
            $table->text('webhook_secret')->nullable();
            $table->string('callback_url')->nullable();
            $table->string('notify_url')->nullable();
            $table->json('metadata')->nullable();
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('moyen_paiements');
    }
};

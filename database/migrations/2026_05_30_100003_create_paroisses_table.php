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
        Schema::create('paroisses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('diocese_id')->nullable()->constrained('dioceses')->nullOnDelete();
            $table->string('nom');
            $table->text('description')->nullable();
            $table->string('telephone')->nullable();
            $table->string('email')->nullable();
            $table->string('adresse')->nullable();
            $table->string('ville')->nullable();
            $table->string('pays')->nullable();
            $table->string('logo')->nullable();
            $table->string('banniere')->nullable();
            $table->string('couleur_principale')->nullable();
            $table->string('statut')->default('en_attente');
            $table->boolean('actif')->default(true);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paroisses');
    }
};

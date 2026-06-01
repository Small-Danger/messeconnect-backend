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
        Schema::create('configuration_paroisses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('paroisse_id')->constrained('paroisses')->cascadeOnDelete();
            $table->string('cle');
            $table->text('valeur')->nullable();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->unique(['paroisse_id', 'cle']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configuration_paroisses');
    }
};

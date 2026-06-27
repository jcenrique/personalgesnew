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
        Schema::create('resultadoinspecciones', function (Blueprint $table) {
            $table->id();

            $table->foreignId('inspeccion_id')->cascadeOnDelete();
            $table->foreignId('elementoinspeccion_id')->cascadeOnDelete();
            $table->boolean('resultado')->default(0);
            $table->text('observacion')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resultadoinspecciones');
    }
};

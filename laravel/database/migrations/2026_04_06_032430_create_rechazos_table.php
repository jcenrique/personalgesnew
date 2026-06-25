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
        Schema::create('rechazos', function (Blueprint $table) {
            $table->id();

 // Usuario que hizo la solicitud rechazada
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // Relación morph con el concepto (Saturday, AdditionalDay, etc.)
            $table->morphs('rechazable');
            // Esto crea: concept_type (string) y concept_id (bigint)

            // Fecha que el usuario había solicitado para disfrutar
            $table->date('fecha_disfrute');

            // Motivo del rechazo
            $table->text('razon')->nullable();

            $table->timestamps();



        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rechazos');
    }
};

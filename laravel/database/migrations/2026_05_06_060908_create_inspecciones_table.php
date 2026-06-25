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
        Schema::create('inspecciones', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id_1')->constrained('users')->nullable()->cascadeOnDelete(); //jefe de servicio
            $table->foreignId('user_id_2')->constrained('users')->nullable()->cascadeOnDelete(); // agente


            $table->dateTime('fecha_hora');

            $table->foreignId('estacion_id');
            $table->enum('type', ['periodica', 'especial'])->default('periodica');
            $table->text('observaciones');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspecciones');
    }
};



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
        Schema::create('disfrutes', function (Blueprint $table) {
            $table->id();

 // Usuario propietario del disfrute
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Relación polimórfica (puede ser dias_adicionales, vacaciones, etc.)
            $table->morphs('disfrutable');

            // Fecha única por usuario
            $table->date('fecha_disfrute');

            //si la relacion es con los "computos", tambien hay que anotar los minutos solicitados
            $table->integer('minutos_solicitados')->default(0)->nullable();

             $table->enum('status', [ 'disponible','solicitado', 'aprobado'])->default('disponible');

            // Garantizar que no se duplique para un mismo usuario en ninguna otra tabla
            $table->unique(['user_id', 'fecha_disfrute']);

            $table->timestamps();


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disfrutes');
    }
};

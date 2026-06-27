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
        Schema::create('training_actions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('course_id')->constrained()->cascadeOnDelete();

            // Datos libres
            $table->string('company_name')->nullable(); // empresa que imparte
            $table->string('trainer_name')->nullable(); // formador

            // Interna o externa
            $table->enum('type', ['interna', 'externa'])->default('interna');

            // Fechas
            $table->date('start_date');
            $table->date('end_date')->nullable();

            // Modalidad
            $table->enum('mode', ['presencial', 'online'])->default('presencial');

            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_actions');
    }
};

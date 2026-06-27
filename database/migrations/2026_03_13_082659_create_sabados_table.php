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
        Schema::create('sabados', function (Blueprint $table) {
            $table->id();
            $table->date('sabado_trabajado');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');



            //crear un indice unico para el sabado trabajado y el user_id
            $table->unique(['sabado_trabajado', 'user_id']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sabados');
    }
};


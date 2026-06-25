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
        Schema::create('computos', function (Blueprint $table) {
            $table->id();
            $table->integer('year')->unsigned();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('disponible');

              // Garantizar que no se duplique para un mismo usuario el computo anual
            $table->unique(['user_id', 'year']);


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('computos');
    }
};

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
        Schema::create('elementos_inspecciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categoriaelemento_id')->cascadeOnDelete();
             $table->string('nombre_es');
            $table->string('nombre_eu');
            $table->boolean('active');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('elementos_inspecciones');
    }
};

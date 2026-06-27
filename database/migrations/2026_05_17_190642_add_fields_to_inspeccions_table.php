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
        Schema::table('inspecciones', function (Blueprint $table) {
            $table->string('tema')->nullable();
            $table->text('cuestiones')->nullable();
            $table->date('fecha_comunicacion')->nullable();
            $table->boolean('actions')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inspecciones', function (Blueprint $table) {
            $table->dropColumn(['tema', 'cuestiones', 'fecha_comunicacion', 'actions']);
        });
    }
};

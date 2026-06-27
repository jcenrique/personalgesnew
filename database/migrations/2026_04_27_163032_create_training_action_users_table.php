<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('training_action_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_action_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->boolean('attended')->default(false);
           


            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_action_user');
    }
};

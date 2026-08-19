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
        Schema::create('valorant_matches', function (Blueprint $table) {
            $table->id();

            $table->string('match_id')->unique(); // O código único da partida
            $table->string('map');                // Nome do mapa
            $table->string('agent');              // Personagem jogado
            $table->integer('kills');
            $table->integer('deaths');
            $table->integer('assists');
            $table->string('result')->nullable(); // Vitória, Derrota ou Empate
            $table->dateTime('played_at')->nullable();
            $table->decimal('hs', 5, 2)->nullable();                 // HS %

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('valorant_matches');
    }
};

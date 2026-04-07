<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('abonne', function (Blueprint $table) {
            $table->id();
            $table->string('nom')->nullable();
            $table->string('prenom')->nullable();
            $table->string('ville')->nullable();
            $table->string('quartier')->nullable();
            $table->string('num_compteur')->unique()->nullable();
            $table->enum('type_abonement', ['dommestique', 'professionnel'])->nullable();
            $table->string('mdp');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('abonne');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facture', function (Blueprint $table) {
            $table->id('idf');
            $table->unsignedBigInteger('id');
            $table->integer('conso');
            $table->float('montant_total');
            $table->date('date_emission')->nullable();
            $table->string('statut');
            $table->foreign('id')->references('id')->on('Abonne');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facture');
    }
};

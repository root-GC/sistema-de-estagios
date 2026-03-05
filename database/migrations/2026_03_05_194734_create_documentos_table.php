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
        Schema::create('documentos', function (Blueprint $table) {
    $table->id();

    $table->foreignId('estagio_id')
          ->constrained('estagios')
          ->cascadeOnDelete();

    $table->enum('tipo', [
        'PDI',
        'PDP',
        'DIARIO',
        'RELATORIO',
        'PORTFOLIO'
    ]);

    $table->string('ficheiro');

    $table->enum('estado', [
        'PENDENTE',
        'APROVADO',
        'REJEITADO'
    ])->default('PENDENTE');

    $table->text('comentario_supervisor')->nullable();

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documentos');
    }
};

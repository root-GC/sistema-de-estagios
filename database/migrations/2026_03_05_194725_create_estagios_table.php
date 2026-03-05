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
       Schema::create('estagios', function (Blueprint $table) {
    $table->id();

    $table->foreignId('estagiario_id')
          ->unique()
          ->constrained('users')
          ->cascadeOnDelete();

    $table->foreignId('supervisor_id')
          ->constrained('users')
          ->restrictOnDelete();

    $table->foreignId('tutor_id')
          ->constrained('users')
          ->restrictOnDelete();

    $table->foreignId('instituicao_id')
          ->constrained('instituicoes')
          ->restrictOnDelete();

    $table->foreignId('curso_id')
          ->constrained('cursos')
          ->restrictOnDelete();

    $table->enum('estado', [
        'EM_ANDAMENTO',
        'CONCLUIDO',
        'REPROVADO'
    ])->default('EM_ANDAMENTO');

    $table->decimal('nota_final', 5, 2)->nullable();

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estagios');
    }
};

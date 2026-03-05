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
        Schema::create('curso_instituicao', function (Blueprint $table) {
    $table->foreignId('curso_id')
          ->constrained('cursos')
          ->cascadeOnDelete();

    $table->foreignId('instituicao_id')
          ->constrained('instituicoes')
          ->cascadeOnDelete();

    $table->primary(['curso_id', 'instituicao_id']);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('curso_instituicao');
    }
};

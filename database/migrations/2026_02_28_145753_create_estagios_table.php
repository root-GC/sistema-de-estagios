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
            $table->foreignId('estagiario_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('supervisor_id')->nullable()->constrained('users');
            $table->foreignId('tutor_id')->nullable()->constrained('users');
            $table->string('curso');
            $table->enum('status', ['ATIVO', 'FINALIZADO', 'CANCELADO'])->default('ATIVO');
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

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
        Schema::create('avaliacao_tutors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estagio_id')->constrained()->onDelete('cascade');
            $table->text('avaliacao');
            $table->decimal('nota', 5, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('avaliacao_tutors');
    }
};

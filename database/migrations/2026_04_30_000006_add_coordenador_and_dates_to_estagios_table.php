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
        Schema::table('estagios', function (Blueprint $table) {
            $table->foreignId('coordenador_id')->nullable()->after('tutor_id')->constrained('users')->restrictOnDelete();
            $table->date('data_inicio')->nullable()->after('curso_id');
            $table->date('data_fim')->nullable()->after('data_inicio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('estagios', function (Blueprint $table) {
            $table->dropForeignIdFor('coordenador_id');
            $table->dropColumn(['coordenador_id', 'data_inicio', 'data_fim']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {

        //Criacao das tabelas user
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');

            $table->foreignId('curso_id')
                ->nullable()
                ->constrained('cursos')
                ->nullOnDelete();

            $table->foreignId('instituicao_id')
                ->nullable()
                ->constrained('instituicoes')
                ->nullOnDelete();

            $table->boolean('ativo')->default(true);

            $table->timestamps();
        });


        // 🔹 Tabela de reset de password
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();

            $table->foreign('email')
                  ->references('email')
                  ->on('users')
                  ->onDelete('cascade');
        });

        // 🔹 Tabela de sessões
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        // 🔹 Reverter apenas o que foi criado
        Schema::dropIfExists('users');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'ativo']);
        });
    }
};
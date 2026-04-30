<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabela users (completa, incluindo remember_token)
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();                     // ← Adicionado (estava em falta)
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });

        // Tabela password_reset_tokens (apenas o essencial, sem colunas extra)
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
            // NOTA: NÃO se coloca remember_token nem email_verified_at aqui
            // E NÃO se coloca foreign key (o email é a chave primária)
        });

        // Tabela de sessões (sem alterações, está correcta)
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
        // Remover as tabelas na ordem inversa da criação (por causa das chaves)
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');

        // NOTA: o bloco abaixo foi removido porque:
        // - As colunas 'role' e 'ativo' já não existem na estrutura actual
        // - Após dropar a tabela 'users', não há nada para alterar
        // - Se precisares de remover colunas futuramente, cria uma migration separada
    }
};
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,  // 1. Criar roles e permissions
            DepartamentoSeeder::class,    // 2. Criar departamentos
            CursoSeeder::class,           // 3. Criar cursos
            UserSeeder::class,            // 4. Criar usuários admin e teste
            StaffProfileSeeder::class,    // 5. Criar perfis de staff
            StudentProfileSeeder::class,  // 6. Criar perfis de estudante
            InstituicaoSeeder::class,     // 7. Criar instituições parceiras
            EstagioSeeder::class,         // 8. Criar estágios
        ]);
    }
}
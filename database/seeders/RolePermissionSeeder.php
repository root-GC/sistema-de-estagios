<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Criar roles com guard 'web' (padrão)
        $roles = ['admin', 'coordenador', 'supervisor', 'tutor', 'estudante', 'chefe_repartição'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        // Criar permissions
        $permissions = [
            'criar-usuarios',
            'editar-usuarios',
            'deletar-usuarios',
            'ver-estagios',
            'avaliar-estagios',
            'enviar-documentos',
            'aprovar-documentos',
            'gerenciar-papeis',
            'ver-relatorios',
            'exportar-pauta',
            'publicar-pauta',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Atribuir permissions a roles
        $admin = Role::findByName('admin', 'web');
        $admin->syncPermissions(Permission::all());

        $coordenador = Role::findByName('coordenador', 'web');
        $coordenador->syncPermissions([
            'criar-usuarios',
            'ver-estagios',
            'avaliar-estagios',
            'aprovar-documentos',
            'ver-relatorios',
            'exportar-pauta',
            'publicar-pauta',
        ]);

        $supervisor = Role::findByName('supervisor', 'web');
        $supervisor->syncPermissions([
            'ver-estagios',
            'avaliar-estagios',
            'aprovar-documentos',
        ]);

        $tutor = Role::findByName('tutor', 'web');
        $tutor->syncPermissions([
            'ver-estagios',
            'avaliar-estagios',
            'enviar-documentos',
            'aprovar-documentos',
        ]);

        $estudante = Role::findByName('estudante', 'web');
        $estudante->syncPermissions([
            'ver-estagios',
            'enviar-documentos',
        ]);

        $chefe = Role::findByName('chefe_repartição', 'web');
        $chefe->syncPermissions([
            'criar-usuarios',
            'editar-usuarios',
            'ver-estagios',
            'ver-relatorios',
        ]);
    }
}
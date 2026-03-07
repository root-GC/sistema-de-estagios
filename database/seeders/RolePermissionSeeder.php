<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
{
    // Roles
    $roles = ['ADMIN','COORDENADOR','SUPERVISOR','TUTOR','ESTAGIARIO','CHEFE_REPARTICAO'];
    foreach($roles as $role){
        Role::firstOrCreate(['name'=>$role]);
    }

    // Permissions
    $permissions = [
        'criar usuarios',
        'editar usuarios',
        'deletar usuarios',
        'ver estagios',
        'avaliar estagios',
        'enviar documentos',
        'aprovar documentos'
    ];

    foreach($permissions as $permission){
        Permission::firstOrCreate(['name'=>$permission]);
    }

    // Exemplo: atribuir permissões a roles
    Role::findByName('ADMIN')->givePermissionTo(Permission::all());
    Role::findByName('COORDENADOR')->givePermissionTo(['criar usuarios','ver estagios']);
    Role::findByName('SUPERVISOR')->givePermissionTo(['avaliar estagios']);
    Role::findByName('TUTOR')->givePermissionTo(['avaliar estagios','enviar documentos']);
    }
}
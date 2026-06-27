<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class RolePermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $forbiddenRoles = ['admin', 'super_admin'];

        $roles = Role::whereNotIn('name', $forbiddenRoles)->get();

        if ($roles->isEmpty()) {
            $this->command->warn('No existen roles válidos para asignar permisos.');
            return;
        }

        $permissionNames = [
            'ViewAny:Additionalday',
            'ViewAny:Sabado',
            'ViewAny:Computo',
            'View:Computo',
            'ViewAny:Rechazo',
            'Delete:Rechazo',
            'DeleteAny:Rechazo',
            'ViewAny:Course',
            'View:TrainingAction',
            'View:Dashboard',
            'View:CalendarioPersonalWidget',
        ];

        foreach ($permissionNames as $permissionName) {
            Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web',
            ]);
        }

        foreach ($roles as $role) {
            $role->givePermissionTo($permissionNames);
        }

        $this->command->info('Permisos asignados a los roles excepto admin y super_admin.');
    }
}

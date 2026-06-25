<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class RandomUserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $excludeEmail = 'jcenrique@free.fr';
        $forbiddenRoles = ['admin', 'super_admin'];

        $roles = Role::whereNotIn('name', $forbiddenRoles)
            ->pluck('name')
            ->all();

        if (empty($roles)) {
            $this->command->warn('No existen roles válidos para asignar.');
            return;
        }

        User::where('email', '<>', $excludeEmail)
            ->get()
            ->each(function (User $user) use ($roles) {
                $randomRole = $roles[array_rand($roles)];
                $user->syncRoles([$randomRole]);
            });

        $this->command->info('Roles aleatorios asignados a los usuarios, excepto a ' . $excludeEmail);
    }
}

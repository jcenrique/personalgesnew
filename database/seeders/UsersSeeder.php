<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Instanciamos Faker en español para que los nombres suenen acorde
        $faker = \Faker\Factory::create('es_ES');

        $roles = [
            'super_admin',
            'admin',
            'jefe_servicio',
            'tecnico_pm',
            'tecnico_pm_integral',
            'tecnico_red',
            'tecnico_red_habilitado',
        ];

        $user = User::where('email', 'jcenrique@free.fr')->first();
        if (!$user) {

            $user =  User::firstOrCreate([
                'name' => 'Juan Carlos Enrique',
                // Generamos un email único basado en el rol para que sea fácil de recordar
                // Ej: tecnico_pm.1@ferro.com, tecnico_pm.2@ferro.com...
                'email' => 'jcenrique@free.fr',
                'role' => 'super_admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]);
        }
        $user->assignRole('super_admin');

        foreach ($roles as $roleName) {
            // 1. Aseguramos que el rol exista
            $role = Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web'
            ]);

            // 2. Creamos 5 usuarios por rol
            foreach (range(1, 5) as $index) {
                $name = $faker->name;
                $user = User::create([
                    'name' => $name,
                    // Generamos un email único basado en el rol para que sea fácil de recordar
                    // Ej: tecnico_pm.1@ferro.com, tecnico_pm.2@ferro.com...
                    'email' => str($name)->slug('.') . ".{$index}@free.fr",
                    'role' => $role->name,
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                    'remember_token' => Str::random(10),
                ]);

                // 3. Asignamos el rol al usuario
                $user->assignRole($role);
            }
        }
    }
}

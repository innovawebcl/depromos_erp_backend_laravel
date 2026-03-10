<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\Role;
use App\Models\RoleModulePermission;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class BackofficeSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            ['key' => 'products', 'name' => 'Productos'],
            ['key' => 'inventory', 'name' => 'Inventario'],
            ['key' => 'banners', 'name' => 'Banners'],
            ['key' => 'orders', 'name' => 'Pedidos'],
            ['key' => 'picking', 'name' => 'Picking'],
            ['key' => 'couriers', 'name' => 'Repartidores'],
            ['key' => 'communes', 'name' => 'Tarifas por Comuna'],
            ['key' => 'customers', 'name' => 'Clientes'],
            ['key' => 'users', 'name' => 'Usuarios'],
            ['key' => 'roles', 'name' => 'Roles y Permisos'],
        ];

        foreach ($modules as $m) {
            Module::updateOrCreate(['key' => $m['key']], ['name' => $m['name'], 'active' => true]);
        }

        $super = Role::updateOrCreate(['name' => 'Super Admin'], []);

        $allModules = Module::query()->get();
        foreach ($allModules as $m) {
            RoleModulePermission::updateOrCreate(
                ['role_id' => $super->id, 'module_id' => $m->id],
                ['enabled' => true]
            );
        }

        // Generar contraseña segura aleatoria para el admin inicial
        $adminPassword = env('ADMIN_DEFAULT_PASSWORD', Str::password(10));

        $admin = User::updateOrCreate(
            ['email' => 'admin@depromos.cl'],
            [
                'name' => 'Admin Depromos',
                'username' => 'admin',
                'password' => Hash::make($adminPassword),
                'role_id' => $super->id,
                'active' => true,
            ]
        );

        $this->command->info('');
        $this->command->info('╔══════════════════════════════════════════════╗');
        $this->command->info('║  CREDENCIALES ADMIN INICIAL                 ║');
        $this->command->info('╠══════════════════════════════════════════════╣');
        $this->command->info("║  Usuario:    admin                          ║");
        $this->command->info("║  Email:      admin@depromos.cl              ║");
        $this->command->info("║  Contraseña: {$adminPassword}");
        $this->command->info('╠══════════════════════════════════════════════╣');
        $this->command->warn('║  CAMBIE ESTA CONTRASEÑA INMEDIATAMENTE      ║');
        $this->command->info('╚══════════════════════════════════════════════╝');
        $this->command->info('');
    }
}

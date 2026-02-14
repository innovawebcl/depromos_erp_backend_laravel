<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\Role;
use App\Models\RoleModulePermission;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

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

        User::updateOrCreate(
            ['email' => 'admin@demo.cl'],
            [
                'name' => 'Admin Demo',
                'username' => 'admin',
                'password' => Hash::make('admin123'),
                'role_id' => $super->id,
                'active' => true,
            ]
        );
    }
}

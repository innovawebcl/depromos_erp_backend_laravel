
<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Module;
use App\Models\RoleModulePermission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_returns_token_in_expected_shape(): void
    {
        $role = Role::create(['name' => 'SuperAdmin']);
        $module = Module::create(['key'=>'products','name'=>'Gestión de Productos','active'=>true]);
        RoleModulePermission::create(['role_id'=>$role->id,'module_id'=>$module->id,'enabled'=>true]);

        $user = User::factory()->create([
            'username' => 'admin1',
            'password' => Hash::make('secret12'),
            'role_id' => $role->id,
        ]);

        $resp = $this->postJson('/api/login', ['username'=>'admin1','password'=>'secret12'])
            ->assertOk()
            ->assertJsonStructure(['data' => ['token']]);

        $this->assertNotEmpty($resp->json('data.token'));
    }
}

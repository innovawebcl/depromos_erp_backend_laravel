
<?php

namespace Tests\Feature;

use App\Models\{Customer,Commune,Product,ProductSize,Order,OrderItem,Role,Module,RoleModulePermission,User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Firebase\JWT\JWT;
use Tests\TestCase;

class PickingTest extends TestCase
{
    use RefreshDatabase;

    private function authHeader(User $user): array
    {
        $payload = [
            'id' => $user->id,
            'name' => $user->name,
            'username' => $user->username,
            'email' => $user->email,
            'role' => 'admin',
            'admin_role' => (string)$user->role_id,
            'first_name' => null,
            'last_name' => null,
            'first_login' => false,
            'iat' => time(),
            'exp' => time() + 3600,
        ];
        $token = JWT::encode($payload, config('backoffice.jwt_secret'), 'HS256');
        return ['Authorization' => 'Bearer '.$token];
    }

    public function test_cannot_close_picking_if_missing_items(): void
    {
        // permisos picking
        $role = Role::create(['name' => 'Picker']);
        $m = Module::create(['key'=>'picking','name'=>'Picking','active'=>true]);
        RoleModulePermission::create(['role_id'=>$role->id,'module_id'=>$m->id,'enabled'=>true]);

        $user = User::factory()->create([
            'username' => 'picker1',
            'password' => Hash::make('secret12'),
            'role_id' => $role->id,
        ]);

        $customer = Customer::create(['name'=>'Cliente 1']);
        $commune = Commune::create(['name'=>'Santiago']);
        $product = Product::create(['code'=>'P001','name'=>'Polera','price'=>1000,'active'=>true]);
        $size = ProductSize::create(['product_id'=>$product->id,'size'=>'M','barcode'=>'P001-M','stock'=>10,'active'=>true]);

        $order = Order::create([
            'customer_id'=>$customer->id,
            'commune_id'=>$commune->id,
            'status'=>'picking',
            'delivery_fee'=>0,
            'total'=>1000,
        ]);

        $item = OrderItem::create([
            'order_id'=>$order->id,
            'product_id'=>$product->id,
            'product_size_id'=>$size->id,
            'quantity'=>2,
            'unit_price'=>1000,
        ]);

        // escanea solo 1 de 2
        $this->postJson("/api/orders/{$order->id}/picking/scan", [
            'order_item_id' => $item->id,
            'scanned_code' => 'P001-M',
            'qty' => 1,
        ], $this->authHeader($user))->assertCreated();

        // cerrar debe fallar
        $this->postJson("/api/orders/{$order->id}/picking/close", [], $this->authHeader($user))
            ->assertStatus(422);
    }
}

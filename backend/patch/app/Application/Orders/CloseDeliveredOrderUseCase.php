<?php

namespace App\Application\Orders;

use App\Domain\Common\Result;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class CloseDeliveredOrderUseCase
{
    public function execute(int $orderId, ?string $receiverRut = null, ?string $photoUrl = null, ?int $userId = null): Result
    {
        $order = Order::query()->find($orderId);
        if (!$order) return Result::fail('Pedido no encontrado', 404);

        if ($order->status !== 'en_route') {
            return Result::fail('Solo se puede cerrar desde estado En ruta', 422);
        }

        return DB::transaction(function () use ($order, $receiverRut, $photoUrl, $userId) {
            $order->status = 'delivered';
            $order->receiver_rut = $receiverRut;
            $order->delivery_photo_url = $photoUrl;
            $order->save();

            DB::table('order_status_history')->insert([
                'order_id' => $order->id,
                'from_status' => 'en_route',
                'to_status' => 'delivered',
                'changed_by_user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return Result::ok($order);
        });
    }
}

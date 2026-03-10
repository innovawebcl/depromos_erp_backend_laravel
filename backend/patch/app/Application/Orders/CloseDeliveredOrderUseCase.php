<?php

namespace App\Application\Orders;

use App\Domain\Common\Result;
use App\Domain\Orders\OrderStatus;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class CloseDeliveredOrderUseCase
{
    public function execute(int $orderId, ?string $receiverRut = null, ?string $photoUrl = null, ?int $userId = null): Result
    {
        $order = Order::query()->find($orderId);
        if (!$order) return Result::fail('Pedido no encontrado', 404);

        $currentStatus = $order->status;
        $targetStatus = OrderStatus::Delivered;

        if (!$currentStatus->canTransitionTo($targetStatus)) {
            return Result::fail("No se puede transicionar de '{$currentStatus->label()}' a '{$targetStatus->label()}'", 422);
        }

        return DB::transaction(function () use ($order, $currentStatus, $targetStatus, $receiverRut, $photoUrl, $userId) {
            $order->status = $targetStatus;
            $order->receiver_rut = $receiverRut;
            $order->delivery_photo_url = $photoUrl;
            $order->save();

            DB::table('order_status_history')->insert([
                'order_id' => $order->id,
                'from_status' => $currentStatus->value,
                'to_status' => $targetStatus->value,
                'changed_by_user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return Result::ok($order);
        });
    }
}

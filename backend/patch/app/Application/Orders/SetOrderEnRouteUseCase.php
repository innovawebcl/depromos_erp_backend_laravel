<?php

namespace App\Application\Orders;

use App\Domain\Common\Result;
use App\Domain\Orders\OrderStatus;
use App\Domain\Orders\Ports\PushNotifier;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class SetOrderEnRouteUseCase
{
    public function __construct(private readonly PushNotifier $push) {}

    public function execute(int $orderId, int $etaMinutes, ?int $userId = null): Result
    {
        $order = Order::query()->find($orderId);
        if (!$order) return Result::fail('Pedido no encontrado', 404);

        $currentStatus = $order->status;
        $targetStatus = OrderStatus::EnRoute;

        if (!$currentStatus->canTransitionTo($targetStatus)) {
            return Result::fail("No se puede transicionar de '{$currentStatus->label()}' a '{$targetStatus->label()}'", 422);
        }

        return DB::transaction(function () use ($order, $currentStatus, $targetStatus, $etaMinutes, $userId) {
            $order->status = $targetStatus;
            $order->eta_minutes = $etaMinutes;
            $order->save();

            DB::table('order_status_history')->insert([
                'order_id' => $order->id,
                'from_status' => $currentStatus->value,
                'to_status' => $targetStatus->value,
                'changed_by_user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Notificación push (adapter)
            $this->push->notifyOrderEnRoute($order->id, $order->customer_id, $etaMinutes);

            return Result::ok($order);
        });
    }
}

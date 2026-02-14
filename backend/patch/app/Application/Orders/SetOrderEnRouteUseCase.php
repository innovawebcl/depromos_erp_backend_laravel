
<?php

namespace App\Application\Orders;

use App\Domain\Common\Result;
use App\Domain\Orders\Ports\PushNotifier;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class SetOrderEnRouteUseCase
{
    public function __construct(private readonly PushNotifier $push) {}

    public function execute(int $orderId, int $etaMinutes): Result
    {
        $order = Order::query()->find($orderId);
        if (!$order) return Result::fail('Pedido no encontrado', 404);

        if (!in_array($order->status, ['ready','picking','pending'], true)) {
            return Result::fail('Estado inválido para pasar a En ruta', 422);
        }

        return DB::transaction(function () use ($order, $etaMinutes) {
            $from = $order->status;
            $order->status = 'en_route';
            $order->eta_minutes = $etaMinutes;
            $order->save();

            DB::table('order_status_history')->insert([
                'order_id' => $order->id,
                'from_status' => $from,
                'to_status' => 'en_route',
                'changed_by_user_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Notificación push (adapter)
            $this->push->notifyOrderEnRoute($order->id, $order->customer_id, $etaMinutes);

            return Result::ok($order);
        });
    }
}

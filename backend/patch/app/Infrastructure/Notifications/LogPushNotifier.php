<?php

namespace App\Infrastructure\Notifications;

use App\Domain\Orders\Ports\PushNotifier;
use Illuminate\Support\Facades\Log;

class LogPushNotifier implements PushNotifier
{
    public function notifyOrderEnRoute(int $orderId, int $customerId, int $etaMinutes): void
    {
        Log::info('Push: order en_route', [
            'order_id' => $orderId,
            'customer_id' => $customerId,
            'eta_minutes' => $etaMinutes,
        ]);
    }
}

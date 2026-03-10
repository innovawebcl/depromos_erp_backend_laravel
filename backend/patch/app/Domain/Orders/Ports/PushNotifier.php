<?php

namespace App\Domain\Orders\Ports;

interface PushNotifier
{
    public function notifyOrderEnRoute(int $orderId, int $customerId, int $etaMinutes): void;
}

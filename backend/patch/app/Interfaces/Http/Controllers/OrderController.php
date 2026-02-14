
<?php

namespace App\Interfaces\Http\Controllers;

use App\Application\Orders\CloseDeliveredOrderUseCase;
use App\Application\Orders\SetOrderEnRouteUseCase;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController
{
    public function index(Request $request)
    {
        $status = $request->query('status');

        $q = Order::query()->with(['customer','commune','courier','items.product','items.size']);
        if ($status) $q->where('status', $status);

        return response()->json($q->orderByDesc('id')->paginate(20));
    }

    public function show(int $orderId)
    {
        $order = Order::query()->with(['customer','commune','courier','items.product','items.size','pickingSession.scans'])
            ->findOrFail($orderId);
        return response()->json($order);
    }

    public function assignCourier(int $orderId, Request $request)
    {
        $payload = $request->validate(['courier_id' => ['required','integer','exists:couriers,id']]);

        $order = Order::query()->findOrFail($orderId);
        $order->courier_id = (int)$payload['courier_id'];
        $order->save();

        return response()->json($order);
    }

    public function enRoute(int $orderId, Request $request, SetOrderEnRouteUseCase $uc)
    {
        $payload = $request->validate(['eta_minutes' => ['required','integer','min:1','max:10000']]);
        $res = $uc->execute($orderId, (int)$payload['eta_minutes']);

        return $res->ok
            ? response()->json($res->data)
            : response()->json(['message' => $res->error], $res->code);
    }

    public function delivered(int $orderId, Request $request, CloseDeliveredOrderUseCase $uc)
    {
        $payload = $request->validate([
            'receiver_rut' => ['nullable','string','max:20'],
            'delivery_photo_url' => ['nullable','string','max:2048'],
        ]);

        $res = $uc->execute($orderId, $payload['receiver_rut'] ?? null, $payload['delivery_photo_url'] ?? null);

        return $res->ok
            ? response()->json($res->data)
            : response()->json(['message' => $res->error], $res->code);
    }
}

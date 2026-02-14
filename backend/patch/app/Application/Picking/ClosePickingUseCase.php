
<?php

namespace App\Application\Picking;

use App\Domain\Common\Result;
use App\Models\Order;
use App\Models\PickingScan;
use App\Models\PickingSession;
use Illuminate\Support\Facades\DB;

class ClosePickingUseCase
{
    /**
     * El admin web impedirá cerrar un pedido y tramitar su despacho si:
     * - falta un producto
     * - la talla es incorrecta a la solicitada
     * - se escaneó un ítem equivocado
     *
     * Nota: talla/ítem equivocado se valida en ScanItemUseCase.
     * Aquí validamos que para cada item la suma escaneada == cantidad solicitada.
     */
    public function execute(int $orderId): Result
    {
        $order = Order::query()->with('items')->find($orderId);
        if (!$order) {
            return Result::fail('Pedido no encontrado', 404);
        }

        $session = PickingSession::query()->where('order_id', $orderId)->first();
        if (!$session) {
            return Result::fail('No existe sesión de picking para este pedido', 422);
        }
        if ($session->status !== 'open') {
            return Result::fail('El picking ya está cerrado', 422);
        }

        foreach ($order->items as $item) {
            $scanned = (int) PickingScan::query()
                ->where('picking_session_id', $session->id)
                ->where('order_item_id', $item->id)
                ->sum('scanned_quantity');

            if ($scanned !== (int)$item->quantity) {
                return Result::fail('No se puede cerrar: faltan ítems por escanear', 422);
            }
        }

        return DB::transaction(function () use ($order, $session) {
            $session->status = 'closed';
            $session->save();

            // Estado del pedido pasa a ready (listo para despacho)
            $order->status = 'ready';
            $order->save();

            return Result::ok(['order_id' => $order->id, 'picking_status' => $session->status, 'order_status' => $order->status]);
        });
    }
}

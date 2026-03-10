<?php

namespace App\Application\Picking;

use App\Domain\Common\Result;
use App\Domain\Orders\OrderStatus;
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

        // Obtener todos los conteos de escaneo en una sola query (fix N+1)
        $scannedTotals = PickingScan::query()
            ->where('picking_session_id', $session->id)
            ->groupBy('order_item_id')
            ->selectRaw('order_item_id, SUM(scanned_quantity) as total_scanned')
            ->pluck('total_scanned', 'order_item_id');

        foreach ($order->items as $item) {
            $scanned = (int) ($scannedTotals[$item->id] ?? 0);
            if ($scanned !== (int) $item->quantity) {
                return Result::fail('No se puede cerrar: faltan ítems por escanear', 422);
            }
        }

        $currentStatus = $order->status;
        $targetStatus = OrderStatus::Ready;

        return DB::transaction(function () use ($order, $session, $currentStatus, $targetStatus) {
            $session->status = 'closed';
            $session->save();

            $order->status = $targetStatus;
            $order->save();

            // Registrar cambio de estado en historial
            DB::table('order_status_history')->insert([
                'order_id'           => $order->id,
                'from_status'        => $currentStatus->value,
                'to_status'          => $targetStatus->value,
                'changed_by_user_id' => null,
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);

            return Result::ok([
                'order_id'       => $order->id,
                'picking_status' => $session->status,
                'order_status'   => $targetStatus->value,
            ]);
        });
    }
}

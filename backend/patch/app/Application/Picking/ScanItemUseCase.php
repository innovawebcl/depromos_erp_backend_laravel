
<?php

namespace App\Application\Picking;

use App\Domain\Common\Result;
use App\Models\Order;
use App\Models\PickingScan;
use App\Models\PickingSession;
use Illuminate\Support\Facades\DB;

class ScanItemUseCase
{
    /**
     * Reglas:
     * - No se puede escanear un ítem que no esté en el pedido
     * - No se puede escanear una talla distinta a la solicitada (validación por order_item_id)
     * - No se puede exceder la cantidad del pedido
     */
    public function execute(int $orderId, int $orderItemId, string $scannedCode, int $qty = 1): Result
    {
        $order = Order::query()->with(['items.product', 'items.size'])->find($orderId);
        if (!$order) {
            return Result::fail('Pedido no encontrado', 404);
        }

        $item = $order->items->firstWhere('id', $orderItemId);
        if (!$item) {
            return Result::fail('El ítem no pertenece al pedido', 422);
        }

        // Validación por código (barcode de talla si existe; si no, usa código de producto)
        $expectedCodes = array_filter([
            $item->size?->barcode,
            $item->product?->code,
        ]);

        if (!in_array($scannedCode, $expectedCodes, true)) {
            return Result::fail('Código escaneado no corresponde al producto/talla solicitada', 422);
        }

        return DB::transaction(function () use ($order, $item, $scannedCode, $qty) {
            $session = PickingSession::query()->firstOrCreate(
                ['order_id' => $order->id],
                ['status' => 'open']
            );

            if ($session->status !== 'open') {
                return Result::fail('El picking ya está cerrado', 422);
            }

            $already = (int) PickingScan::query()
                ->where('picking_session_id', $session->id)
                ->where('order_item_id', $item->id)
                ->sum('scanned_quantity');

            if ($already + $qty > (int)$item->quantity) {
                return Result::fail('Cantidad escaneada excede la cantidad del pedido', 422);
            }

            $scan = PickingScan::create([
                'picking_session_id' => $session->id,
                'order_item_id' => $item->id,
                'scanned_code' => $scannedCode,
                'scanned_quantity' => $qty,
            ]);

            return Result::ok($scan);
        });
    }
}

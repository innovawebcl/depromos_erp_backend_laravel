
<?php

namespace App\Interfaces\Http\Controllers;

use App\Application\Picking\ClosePickingUseCase;
use App\Application\Picking\ScanItemUseCase;
use Illuminate\Http\Request;

class PickingController
{
    public function scan(int $orderId, Request $request, ScanItemUseCase $uc)
    {
        $payload = $request->validate([
            'order_item_id' => ['required','integer'],
            'scanned_code' => ['required','string','max:100'],
            'qty' => ['sometimes','integer','min:1','max:1000'],
        ]);

        $res = $uc->execute($orderId, (int)$payload['order_item_id'], $payload['scanned_code'], (int)($payload['qty'] ?? 1));

        return $res->ok
            ? response()->json($res->data, 201)
            : response()->json(['message' => $res->error], $res->code);
    }

    public function close(int $orderId, ClosePickingUseCase $uc)
    {
        $res = $uc->execute($orderId);
        return $res->ok
            ? response()->json($res->data)
            : response()->json(['message' => $res->error], $res->code);
    }
}

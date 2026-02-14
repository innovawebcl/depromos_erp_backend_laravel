
<?php

namespace App\Interfaces\Http\Controllers;

use App\Application\Customers\SetBlacklistUseCase;
use App\Application\Customers\UpdatePurchaseGoalUseCase;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController
{
    public function index()
    {
        return response()->json(Customer::query()->paginate(20));
    }

    public function show(int $id)
    {
        return response()->json(Customer::query()->findOrFail($id));
    }

    public function blacklist(int $id, Request $request, SetBlacklistUseCase $uc)
    {
        $payload = $request->validate(['is_blacklisted' => ['required','boolean']]);
        $res = $uc->execute($id, (bool)$payload['is_blacklisted']);

        return $res->ok
            ? response()->json($res->data)
            : response()->json(['message' => $res->error], $res->code);
    }

    public function purchaseGoal(int $id, Request $request, UpdatePurchaseGoalUseCase $uc)
    {
        $payload = $request->validate(['purchase_goal' => ['required','integer','min:0']]);
        $res = $uc->execute($id, (int)$payload['purchase_goal']);

        return $res->ok
            ? response()->json($res->data)
            : response()->json(['message' => $res->error], $res->code);
    }
}

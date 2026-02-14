
<?php

namespace App\Application\Customers;

use App\Domain\Common\Result;
use App\Models\Customer;

class UpdatePurchaseGoalUseCase
{
    public function execute(int $customerId, int $goal): Result
    {
        if ($goal < 0) return Result::fail('Meta inválida', 422);

        $c = Customer::query()->find($customerId);
        if (!$c) return Result::fail('Cliente no encontrado', 404);

        $c->purchase_goal = $goal;
        $c->save();

        return Result::ok($c);
    }
}

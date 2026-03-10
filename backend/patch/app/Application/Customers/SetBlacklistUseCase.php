<?php

namespace App\Application\Customers;

use App\Domain\Common\Result;
use App\Models\Customer;

class SetBlacklistUseCase
{
    public function execute(int $customerId, bool $blacklisted): Result
    {
        $c = Customer::query()->find($customerId);
        if (!$c) return Result::fail('Cliente no encontrado', 404);

        $c->is_blacklisted = $blacklisted;
        $c->save();

        return Result::ok($c);
    }
}

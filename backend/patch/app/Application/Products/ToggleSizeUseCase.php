
<?php

namespace App\Application\Products;

use App\Domain\Common\Result;
use App\Models\ProductSize;

class ToggleSizeUseCase
{
    public function execute(int $productId, string $size): Result
    {
        $ps = ProductSize::query()->where('product_id', $productId)->where('size', $size)->first();
        if (!$ps) {
            return Result::fail('Talla no encontrada', 404);
        }
        $ps->active = !$ps->active;
        $ps->save();

        return Result::ok($ps);
    }
}

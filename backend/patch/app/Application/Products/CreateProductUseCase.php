<?php

namespace App\Application\Products;

use App\Domain\Common\Result;
use App\Models\Product;
use App\Models\ProductSize;
use Illuminate\Support\Facades\DB;

class CreateProductUseCase
{
    public function execute(array $payload): Result
    {
        return DB::transaction(function () use ($payload) {
            $product = Product::create([
                'code' => $payload['code'],
                'name' => $payload['name'],
                'description' => $payload['description'] ?? null,
                'price' => $payload['price'],
                'photo_url' => $payload['photo_url'] ?? null,
                'active' => (bool)($payload['active'] ?? true),
            ]);

            foreach (($payload['sizes'] ?? []) as $size) {
                ProductSize::create([
                    'product_id' => $product->id,
                    'size' => $size['size'],
                    'barcode' => $size['barcode'] ?? null,
                    'active' => (bool)($size['active'] ?? true),
                    'stock' => (int)($size['stock'] ?? 0),
                ]);
            }

            return Result::ok($product->load('sizes'));
        });
    }
}

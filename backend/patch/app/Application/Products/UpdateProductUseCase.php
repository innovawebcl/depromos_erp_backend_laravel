<?php

namespace App\Application\Products;

use App\Domain\Common\Result;
use App\Models\Product;
use App\Models\ProductSize;
use Illuminate\Support\Facades\DB;

class UpdateProductUseCase
{
    public function execute(int $productId, array $payload): Result
    {
        $product = Product::query()->with('sizes')->find($productId);
        if (!$product) {
            return Result::fail('Producto no encontrado', 404);
        }

        return DB::transaction(function () use ($product, $payload) {
            $product->fill([
                'name' => $payload['name'] ?? $product->name,
                'description' => $payload['description'] ?? $product->description,
                'price' => $payload['price'] ?? $product->price,
                'photo_url' => $payload['photo_url'] ?? $product->photo_url,
                'active' => array_key_exists('active', $payload) ? (bool)$payload['active'] : $product->active,
            ])->save();

            foreach (($payload['sizes'] ?? []) as $size) {
                ProductSize::query()->updateOrCreate(
                    ['product_id' => $product->id, 'size' => $size['size']],
                    [
                        'barcode' => $size['barcode'] ?? null,
                        'active' => (bool)($size['active'] ?? true),
                        'stock' => (int)($size['stock'] ?? 0),
                    ]
                );
            }

            return Result::ok($product->fresh()->load('sizes'));
        });
    }
}

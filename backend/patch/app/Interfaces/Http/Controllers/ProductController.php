<?php

namespace App\Interfaces\Http\Controllers;

use App\Application\Products\CreateProductUseCase;
use App\Application\Products\ToggleSizeUseCase;
use App\Application\Products\UpdateProductUseCase;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController
{
    public function index()
    {
        return response()->json(Product::query()->with('sizes')->paginate(20));
    }

    public function show(int $id)
    {
        return response()->json(Product::query()->with('sizes')->findOrFail($id));
    }

    public function store(Request $request, CreateProductUseCase $uc)
    {
        $payload = $request->validate([
            'code' => ['required','string','max:50','unique:products,code'],
            'name' => ['required','string','max:255'],
            'description' => ['nullable','string'],
            'price' => ['required','numeric','min:0'],
            'photo_url' => ['nullable','string','max:2048'],
            'active' => ['sometimes','boolean'],
            'sizes' => ['sometimes','array'],
            'sizes.*.size' => ['required_with:sizes','string','max:20'],
            'sizes.*.barcode' => ['nullable','string','max:100','unique:product_sizes,barcode'],
            'sizes.*.active' => ['sometimes','boolean'],
            'sizes.*.stock' => ['sometimes','integer','min:0'],
        ]);

        $res = $uc->execute($payload);
        return $res->ok
            ? response()->json($res->data, 201)
            : response()->json(['message' => $res->error], $res->code);
    }

    public function update(int $id, Request $request, UpdateProductUseCase $uc)
    {
        $payload = $request->validate([
            'name' => ['sometimes','string','max:255'],
            'description' => ['nullable','string'],
            'price' => ['sometimes','numeric','min:0'],
            'photo_url' => ['nullable','string','max:2048'],
            'active' => ['sometimes','boolean'],
            'sizes' => ['sometimes','array'],
            'sizes.*.size' => ['required_with:sizes','string','max:20'],
            'sizes.*.barcode' => ['nullable','string','max:100'],
            'sizes.*.active' => ['sometimes','boolean'],
            'sizes.*.stock' => ['sometimes','integer','min:0'],
        ]);

        $res = $uc->execute($id, $payload);
        return $res->ok
            ? response()->json($res->data)
            : response()->json(['message' => $res->error], $res->code);
    }

    public function toggleSize(int $id, string $size, ToggleSizeUseCase $uc)
    {
        $res = $uc->execute($id, $size);
        return $res->ok
            ? response()->json($res->data)
            : response()->json(['message' => $res->error], $res->code);
    }
}

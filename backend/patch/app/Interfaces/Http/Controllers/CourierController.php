<?php

namespace App\Interfaces\Http\Controllers;

use App\Models\Courier;
use Illuminate\Http\Request;

class CourierController
{
    public function index()
    {
        return response()->json(Courier::query()->withAvg('ratings','rating')->paginate(20));
    }

    public function show(int $id)
    {
        return response()->json(Courier::query()->with('ratings')->withAvg('ratings','rating')->findOrFail($id));
    }

    public function store(Request $request)
    {
        $payload = $request->validate([
            'name' => ['required','string','max:255'],
            'phone' => ['nullable','string','max:30'],
            'active' => ['sometimes','boolean'],
        ]);

        $courier = Courier::create($payload);
        return response()->json($courier, 201);
    }

    public function update(int $id, Request $request)
    {
        $courier = Courier::query()->findOrFail($id);

        $payload = $request->validate([
            'name' => ['sometimes','string','max:255'],
            'phone' => ['nullable','string','max:30'],
            'active' => ['sometimes','boolean'],
        ]);

        $courier->fill($payload)->save();
        return response()->json($courier);
    }
}

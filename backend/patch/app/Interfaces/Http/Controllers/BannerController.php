<?php

namespace App\Interfaces\Http\Controllers;

use App\Models\Banner;
use Illuminate\Http\Request;

class BannerController
{
    public function index()
    {
        return response()->json(Banner::query()->orderByDesc('id')->paginate(20));
    }

    public function show(int $id)
    {
        return response()->json(Banner::query()->findOrFail($id));
    }

    public function store(Request $request)
    {
        $payload = $request->validate([
            'title' => ['required','string','max:255'],
            'image_url' => ['required','string','max:2048'],
            'target_url' => ['nullable','string','max:2048'],
            'starts_at' => ['nullable','date'],
            'ends_at' => ['nullable','date','after_or_equal:starts_at'],
            'active' => ['sometimes','boolean'],
        ]);

        $banner = Banner::create($payload);
        return response()->json($banner, 201);
    }

    public function update(int $id, Request $request)
    {
        $banner = Banner::query()->findOrFail($id);

        $payload = $request->validate([
            'title' => ['sometimes','string','max:255'],
            'image_url' => ['sometimes','string','max:2048'],
            'target_url' => ['nullable','string','max:2048'],
            'starts_at' => ['nullable','date'],
            'ends_at' => ['nullable','date','after_or_equal:starts_at'],
            'active' => ['sometimes','boolean'],
        ]);

        $banner->fill($payload)->save();
        return response()->json($banner);
    }

    public function destroy(int $id)
    {
        Banner::query()->whereKey($id)->delete();
        return response()->json(['ok' => true]);
    }
}

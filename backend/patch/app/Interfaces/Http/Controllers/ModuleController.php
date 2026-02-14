<?php

namespace App\Interfaces\Http\Controllers;

use App\Models\Module;

class ModuleController
{
    public function index()
    {
        return response()->json(Module::query()->orderBy('name')->get());
    }
}

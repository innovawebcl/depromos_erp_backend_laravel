<?php

namespace App\Interfaces\Http\Controllers;

use App\Models\Module;
use App\Models\Role;
use App\Models\RoleModulePermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleController
{
    public function index()
    {
        return response()->json(
            Role::query()->with(['permissions.module'])->orderBy('name')->get()
        );
    }

    public function show(int $id)
    {
        $role = Role::query()->with(['permissions.module'])->findOrFail($id);
        return response()->json($role);
    }

    public function store(Request $request)
    {
        $payload = $request->validate([
            'name' => ['required','string','max:255','unique:roles,name'],
        ]);

        $role = Role::create($payload);

        $modules = Module::query()->where('active', true)->get();
        foreach ($modules as $m) {
            RoleModulePermission::updateOrCreate(
                ['role_id' => $role->id, 'module_id' => $m->id],
                ['enabled' => true]
            );
        }

        return response()->json($role->load(['permissions.module']), 201);
    }

    public function update(int $id, Request $request)
    {
        $role = Role::query()->findOrFail($id);

        $payload = $request->validate([
            'name' => ['sometimes','string','max:255','unique:roles,name,'.$role->id],
        ]);

        $role->fill($payload)->save();

        return response()->json($role->load(['permissions.module']));
    }

    public function setModules(int $id, Request $request)
    {
        $role = Role::query()->findOrFail($id);

        $payload = $request->validate([
            'modules' => ['required','array'],
        ]);

        $modules = Module::query()->get()->keyBy('key');

        DB::transaction(function () use ($payload, $modules, $role) {
            foreach ($payload['modules'] as $key => $enabled) {
                if (!$modules->has($key)) {
                    continue;
                }
                $module = $modules->get($key);
                RoleModulePermission::updateOrCreate(
                    ['role_id' => $role->id, 'module_id' => $module->id],
                    ['enabled' => (bool)$enabled]
                );
            }
        });

        return response()->json($role->load(['permissions.module']));
    }
}

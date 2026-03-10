<?php

namespace App\Interfaces\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $q = User::query()->with('role');
        if ($search) {
            $q->where(function ($qq) use ($search) {
                $qq->where('name','like',"%{$search}%")
                   ->orWhere('first_name','like',"%{$search}%")
                   ->orWhere('last_name','like',"%{$search}%")
                   ->orWhere('email','like',"%{$search}%")
                   ->orWhere('username','like',"%{$search}%");
            });
        }

        return response()->json($q->orderByDesc('id')->paginate(20));
    }

    public function show(int $id)
    {
        return response()->json(User::query()->with('role')->findOrFail($id));
    }

    public function store(Request $request)
    {
        $payload = $request->validate([
            'name' => ['required','string','max:255'],
            'first_name' => ['nullable','string','max:255'],
            'last_name' => ['nullable','string','max:255'],
            'username' => ['required','string','max:100','unique:users,username'],
            'email' => ['required','email','max:255','unique:users,email'],
            'password' => ['required','string','min:6'],
            'role_id' => ['required','integer','exists:roles,id'],
            'active' => ['sometimes','boolean'],
        ]);

        $user = User::create([
            'name' => $payload['name'],
            'first_name' => $payload['first_name'] ?? null,
            'last_name' => $payload['last_name'] ?? null,
            'username' => $payload['username'],
            'email' => $payload['email'],
            'password' => Hash::make($payload['password']),
            'role_id' => (int)$payload['role_id'],
            'active' => (bool)($payload['active'] ?? true),
            'first_login' => true,
        ]);

        return response()->json($user->load('role'), 201);
    }

    public function update(int $id, Request $request)
    {
        $user = User::query()->findOrFail($id);

        $payload = $request->validate([
            'name' => ['sometimes','string','max:255'],
            'first_name' => ['nullable','string','max:255'],
            'last_name' => ['nullable','string','max:255'],
            'username' => ['sometimes','string','max:100','unique:users,username,'.$user->id],
            'email' => ['sometimes','email','max:255','unique:users,email,'.$user->id],
            'password' => ['nullable','string','min:6'],
            'role_id' => ['sometimes','integer','exists:roles,id'],
            'active' => ['sometimes','boolean'],
        ]);

        if (array_key_exists('password', $payload) && $payload['password']) {
            $user->password = Hash::make($payload['password']);
        }
        unset($payload['password']);

        $user->fill($payload)->save();

        return response()->json($user->load('role'));
    }

    public function toggleActive(int $id)
    {
        $user = User::query()->findOrFail($id);
        $user->active = !$user->active;
        $user->save();
        return response()->json($user->load('role'));
    }
}

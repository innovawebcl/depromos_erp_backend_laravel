<?php

namespace App\Interfaces\Http\Controllers;

use App\Application\Auth\LoginUseCase;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController
{
    public function login(Request $request, LoginUseCase $uc)
    {
        $data = $request->validate([
            'username' => ['required','string','min:4'],
            'password' => ['required','string','min:6'],
        ]);

        $res = $uc->execute($data['username'], $data['password']);

        if (!$res->ok) {
            return response()->json(['message' => $res->error], $res->code);
        }

        // Formato esperado por front Angular: { data: { token: string } }
        return response()->json(['data' => ['token' => $res->data['token']]]);
    }

    public function me(Request $request)
    {
        $userId = (int) $request->attributes->get('user_id');
        $user = User::query()->with('role.permissions.module')->find($userId);

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        return response()->json(['data' => $user]);
    }
}

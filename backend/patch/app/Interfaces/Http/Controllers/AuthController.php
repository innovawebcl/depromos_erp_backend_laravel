
<?php

namespace App\Interfaces\Http\Controllers;

use App\Application\Auth\LoginUseCase;
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
}

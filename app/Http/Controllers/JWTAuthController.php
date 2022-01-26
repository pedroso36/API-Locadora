<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class JWTAuthController extends Controller
{
    //
    public function login(Request $request)
    {
        $credenciais = $request->all(['email', 'password']);

        $token = auth('api')->attempt($credenciais);

        if ($token) {
            return response()->json(['token' => $token]);
        } else {

            return response()->json(['erro' => 'Usuario ou senha estão incorretos !'], 403);
        }
    }

    public function logout()
    {
        $token = auth('api')->logout();
        return response()->json(['msg' => 'Este token não é mais valido.']);
    }

    public function refresh()
    {
        $token = auth('api')->refresh();
        return response()->json(['$token' => $token]);
    }

    public function me()
    {
        return response()->json(auth()->user());
    }
}

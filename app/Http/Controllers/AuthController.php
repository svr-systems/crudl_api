<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            //Modifications
            $request->merge(
                ["email" => strtolower(trim($request->email))],
                ["password" => trim($request->password)]
            );

            //Validations
            $data = $request->validate([
                "email" => "required|string|email",
                "password" => "required|string"
            ]);

            if (!Auth::attempt($data)) {
                return response()->json([
                    "auth" => false,
                    "message" => "Datos de acceso inválidos"
                ], 200);
            }

            $user = $request->user();

            if ($user->active === 0) {
                return response()->json([
                    "auth" => false,
                    "message" => "Usuario inactivo",
                ], 200);
            }

            //Create token
            $tokenResult = $user->createToken("authToken");
            $token = $tokenResult->token;
            $token->save();

            //Data for return
            $user = DB::table("users AS u")
                ->select(
                    "u.id",
                    "u.name",
                    "u.email"
                )
                ->where("u.id", Auth::id())
                ->get();

            return response()->json([
                "auth" => true,
                "message" => "Datos de acceso validos",
                "id" => $user[0]->id,
                "name" => $user[0]->name,
                "email" => $user[0]->email,
                "token" => $tokenResult->accessToken,
                "sidebar" => false
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                "auth" => false,
                "message" => "ERR. " . $th
            ], 200);
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->token()->revoke();

            return response()->json([
                "success" => true,
                "message" => "Cierre de sesión correcto"
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                "success" => false,
                "message" => "ERR. " . $th
            ], 200);
        }
    }
}

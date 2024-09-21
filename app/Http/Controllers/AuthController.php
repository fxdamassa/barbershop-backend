<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request): \Illuminate\Http\JsonResponse
    {
        $credentials = $request->only('email', 'password');

        if (auth()->attempt($credentials)) {
            $user = auth()->user();
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
               'access_token' => $token,
               'token_type' => 'Bearer',
            ]);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    public function register(Request $request) {
        $validator = Validator::make ( $request->all(), [], [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => Hash::make($request['password']),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
           'message' => 'Cadastro realizado com sucesso!',
           'access_token' => $token,
           'token_type' => 'Bearer',
        ], 201);
    }

    public function logout(Request $request) {
            $request->user()->currentAccessToken()->delete();

            return response()->json(['message' => 'Logout realizado com sucesso!'], 200);

    }
}


<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Invalid login credentials.'
            ], 401);
        }

        $user = Auth::user();

        $token = $user->createToken('AppToken')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'role' => $user->role,
        ]);
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

    public function logout(Request $request)
    {
        if ($request->user()) {
            $request->user()->currentAccessToken()->delete();
            return response()->json([
                'message' => 'Logout successful'
            ]);
        } else {
            return response()->json([
                'message' => 'Usuário não autenticado ou token inválido'
            ], 401);
        }
    }


}


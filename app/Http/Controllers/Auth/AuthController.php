<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * LOGIN (API + React)
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Credenciais inválidas'], 401);
        }

        $user = Auth::user();

        $token = $user->createToken('auth_token')->plainTextToken;

        // Spatie role
        $role = $user->getRoleNames()->first();

        return response()->json([
            'user' => $user,
            'role' => $role,   // ✔ correto
            'token' => $token
        ]);
    }

    /**
     * LOGOUT (API)
     */
    public function logout(Request $request)
    {
        // revoga token atual
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout successful'
        ]);
    }

    /**
     * USER LOGADO
     */
    public function me(Request $request)
    {
        return response()->json([
            'user' => $request->user()
        ]);
    }

    /**
     * REFRESH (opcional - recria token)
     */
    public function refresh(Request $request)
    {
        $user = $request->user();

        // remove token atual
        $user->currentAccessToken()->delete();

        // cria novo token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token
        ]);
    }

    /**
     * FORGOT PASSWORD (placeholder)
     */
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        return response()->json([
            'message' => 'Password reset link sent (not implemented yet)'
        ]);
    }

    /**
     * RESET PASSWORD (placeholder)
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed'
        ]);

        return response()->json([
            'message' => 'Password reset successful (not implemented yet)'
        ]);
    }
}
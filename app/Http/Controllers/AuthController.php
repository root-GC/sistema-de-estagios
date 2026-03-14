<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;


class AuthController extends Controller
{
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

         // pega o nome do primeiro role do Spatie
        $role = $user->getRoleNames()->first(); // retorna string ou null

        return response()->json([
            'user' => $user,
            'role' => $user->$role,
            'token' => $token
        ]);
    }

    public function me()
    {
        Log::info(Auth::user());
        return response()->json(Auth::user());
    }

    public function logout(Request $request)
    {
        /** @var \Laravel\Sanctum\PersonalAccessToken $token */
        $token = $request->user()->currentAccessToken();
        
        $token->delete();

        return response()->json(['message' => 'Logout ok']);
    }

    public function recover(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'Email não encontrado'], 404);
        }

        $plainToken = Str::random(60); // token “limpo” para enviar no link

        // salva token hash na base
        DB::table('password_resets')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => Hash::make($plainToken),
                'created_at' => now()
            ]
        );
        $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');
        // Aqui irias enviar email com o link real
        $resetLink = "{$frontendUrl}/reset-password?token={$plainToken}&email={$user->email}";

        // Para dev, podemos apenas logar o link
        Log::info("Link de recuperação: $resetLink");

        return response()->json([
            'message' => 'Link de recuperação enviado. Verifique o log ou o email.',
            'reset_link' => $resetLink // opcional, só para dev
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|min:8|confirmed'
        ]);

        $reset = DB::table('password_resets')
            ->where('email', $request->email)
            ->first();

        if (!$reset) {
            return response()->json(['message' => 'Pedido inválido'], 400);
        }

        // Confere token hash
        if (!Hash::check($request->token, $reset->token)) {
            return response()->json(['message' => 'Token inválido'], 400);
        }

        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        DB::table('password_resets')->where('email', $request->email)->delete();

        return response()->json(['message' => 'Senha redefinida com sucesso']);
    }

}

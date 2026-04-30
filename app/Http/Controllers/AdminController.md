<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Log;
use App\Models\Log;  
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AdminController extends Controller
{

    //  public function logs()
    // {
    //     return Log::latest()->limit(100)->get();
    // }
     // Listar todos os logs mais recentes primeiro
    public function logs()
    {
        return Log::with('user')->latest()->get();
    }

    // Criar um log (exemplo: uso interno pelo sistema)
    public function criarLog($acao)
    {
        return Log::create([
            'user_id' => Auth::id(),
            'acao' => $acao
        ]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

     public function usersAtivos(){
        $user = User::where('ativo', true)->get();
        return response()->json($user);
    }
}

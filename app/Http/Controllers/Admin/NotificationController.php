<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notificacao;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Listar notificações do utilizador autenticado
     */
    public function index()
    {
        $notifications = Auth::user()->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json($notifications);
    }

    /**
     * Ver notificação específica
     */
    public function show(Notificacao $notificacao)
    {
        return response()->json($notificacao);
    }

    /**
     * Marcar como lida
     */
    public function markAsRead(Notificacao $notificacao)
    {
        $notificacao->update(['lida_em' => now()]);
        return response()->json(['message' => 'Notification marked as read']);
    }

    /**
     * Marcar todas como lidas
     */
    public function markAllAsRead()
    {
        Auth::user()->notifications()
            ->whereNull('lida_em')
            ->update(['lida_em' => now()]);

        return response()->json(['message' => 'All notifications marked as read']);
    }

    /**
     * Eliminar notificação
     */
    public function destroy(Notificacao $notificacao)
    {
        $notificacao->delete();
        return response()->json(['message' => 'Notification deleted']);
    }
}

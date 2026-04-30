<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use App\Models\Estagio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DocumentoController extends Controller
{
    public function aprovar(Request $request, $id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user) {
            abort(401, 'Não autenticado');
        }

        $documento = Documento::findOrFail($id);
        $estagio = Estagio::findOrFail($documento->estagio_id);

        // 🔐 Validação contextual real
        if ($estagio->supervisor_id !== $user->id) {
            abort(403, 'Acesso negado');
        }

        $documento->estado = 'APROVADO';
        $documento->save();

        return response()->json(['message' => 'Documento aprovado']);
    }

    public function rejeitar(Request $request, $id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user) {
            abort(401, 'Não autenticado');
        }

        $documento = Documento::findOrFail($id);
        $estagio = Estagio::findOrFail($documento->estagio_id);

        if ($estagio->supervisor_id !== $user->id) {
            abort(403, 'Acesso negado');
        }

        $documento->estado = 'REJEITADO';
        $documento->feedback = $request->input('feedback');
        $documento->save();

        return response()->json(['message' => 'Documento rejeitado']);
    }
}
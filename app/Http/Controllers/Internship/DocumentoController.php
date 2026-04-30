<?php

namespace App\Http\Controllers\Internship;

use App\Http\Controllers\Controller;
use App\Models\Documento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentoController extends Controller
{
    /**
     * Listar documentos
     */
    public function index(Request $request)
    {
        $query = Documento::with('estagio');

        if ($request->has('estagio_id')) {
            $query->where('estagio_id', $request->estagio_id);
        }

        if ($request->has('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->has('estado')) {
            $query->where('estado', $request->estado);
        }

        $documentos = $query->paginate(15);
        return response()->json($documentos);
    }

    /**
     * Ver documento específico
     */
    public function show(Documento $documento)
    {
        return response()->json($documento->load('estagio'));
    }

    /**
     * Upload de documento
     */
    public function upload(Request $request)
    {
        $this->authorize('create', Documento::class);

        $validated = $request->validate([
            'estagio_id' => 'required|exists:estagios,id',
            'tipo' => 'required|in:PDI,PDP,PlanoAtividades,DiarioReflexivo,Projeto,Portfolio,RelatorioFinal',
            'ficheiro' => 'required|file|max:10240',
        ]);

        $path = Storage::disk('public')->put('documentos', $request->file('ficheiro'));

        $documento = Documento::create([
            'estagio_id' => $validated['estagio_id'],
            'tipo' => $validated['tipo'],
            'ficheiro' => $path,
            'estado' => 'pendente_revisao',
            'submetido_em' => now()
        ]);

        return response()->json($documento, 201);
    }

    /**
     * Download de documento
     */
    public function download(Documento $documento)
    {
        if (!Storage::disk('public')->exists($documento->ficheiro)) {
            return response()->json(['message' => 'File not found'], 404);
        }

        return Storage::disk('public')->download($documento->ficheiro);
    }

    /**
     * Aprovar documento
     */
    public function approve(Request $request, Documento $documento)
    {
        $this->authorize('approve', $documento);

        $validated = $request->validate([
            'comentario_supervisor' => 'nullable|string'
        ]);

        $documento->update([
            'estado' => 'aprovado',
            'comentario_supervisor' => $validated['comentario_supervisor'] ?? null
        ]);

        return response()->json(['message' => 'Document approved']);
    }

    /**
     * Rejeitar documento
     */
    public function reject(Request $request, Documento $documento)
    {
        $this->authorize('reject', $documento);

        $validated = $request->validate([
            'comentario_supervisor' => 'required|string'
        ]);

        $documento->update([
            'estado' => 'rejeitado',
            'comentario_supervisor' => $validated['comentario_supervisor']
        ]);

        return response()->json(['message' => 'Document rejected']);
    }

    /**
     * Comentário no documento
     */
    public function comment(Request $request, Documento $documento)
    {
        $this->authorize('comment', $documento);

        $validated = $request->validate([
            'comentario_supervisor' => 'required|string'
        ]);

        $documento->update(['comentario_supervisor' => $validated['comentario_supervisor']]);
        return response()->json(['message' => 'Comment added']);
    }

    /**
     * Eliminar documento
     */
    public function destroy(Documento $documento)
    {
        $this->authorize('delete', $documento);

        if (Storage::disk('public')->exists($documento->ficheiro)) {
            Storage::disk('public')->delete($documento->ficheiro);
        }

        $documento->delete();
        return response()->json(['message' => 'Document deleted']);
    }
}

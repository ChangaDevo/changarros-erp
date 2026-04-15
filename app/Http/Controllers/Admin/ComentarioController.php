<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comentario;
use App\Models\Entrega;
use App\Models\Documento;
use Illuminate\Http\Request;

class ComentarioController extends Controller
{
    private array $tiposPermitidos = [
        'App\\Models\\Entrega',
        'App\\Models\\Documento',
    ];

    public function store(Request $request)
    {
        $request->validate([
            'comentable_type' => 'required|in:App\\Models\\Entrega,App\\Models\\Documento',
            'comentable_id'   => 'required|integer',
            'contenido'       => 'required|string|max:2000',
        ]);

        $model = $request->comentable_type::findOrFail($request->comentable_id);

        Comentario::create([
            'comentable_type' => $request->comentable_type,
            'comentable_id'   => $request->comentable_id,
            'user_id'         => auth()->id(),
            'contenido'       => $request->contenido,
        ]);

        return back()->with('success', 'Comentario agregado.');
    }

    public function destroy(Comentario $comentario)
    {
        // Solo el autor o superadmin puede eliminar
        if ($comentario->user_id !== auth()->id() && !auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $comentario->delete();
        return back()->with('success', 'Comentario eliminado.');
    }
}

<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Comentario;
use Illuminate\Http\Request;

class ComentarioController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'comentable_type' => 'required|in:App\\Models\\Entrega,App\\Models\\Documento',
            'comentable_id'   => 'required|integer',
            'contenido'       => 'required|string|max:2000',
        ]);

        $model = $request->comentable_type::findOrFail($request->comentable_id);

        // Verificar que el modelo pertenece al cliente autenticado
        $proyecto = $model->proyecto;
        if (!$proyecto || $proyecto->cliente_id !== auth()->user()->cliente_id) {
            abort(403);
        }

        Comentario::create([
            'comentable_type' => $request->comentable_type,
            'comentable_id'   => $request->comentable_id,
            'user_id'         => auth()->id(),
            'contenido'       => $request->contenido,
        ]);

        return back()->with('success', 'Comentario enviado.');
    }
}

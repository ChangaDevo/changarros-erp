<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use App\Models\MarcaRecurso;
use Illuminate\Support\Facades\Storage;

class MarcaPublicaController extends Controller
{
    public function show(string $token)
    {
        $marca = Marca::where('token_publico', $token)->firstOrFail();

        // Verificar acceso
        if (!$marca->acceso_cliente) {
            abort(403, 'Esta marca no está disponible públicamente en este momento.');
        }

        $logos       = $marca->logos()->get();
        $tipografias = $marca->tipografias()->get();
        $colores     = $marca->colores()->get();
        $templates   = $marca->templates()->get();
        $otros       = $marca->otros()->get();

        return view('marca-publica', compact('marca', 'logos', 'tipografias', 'colores', 'templates', 'otros'));
    }

    public function descargar(string $token, MarcaRecurso $recurso)
    {
        $marca = Marca::where('token_publico', $token)->firstOrFail();

        abort_unless($marca->acceso_cliente, 403);
        abort_unless($recurso->marca_id === $marca->id, 404);
        abort_unless($recurso->archivo_path, 404);

        return Storage::disk('public')->download(
            $recurso->archivo_path,
            $recurso->archivo_nombre_original ?? basename($recurso->archivo_path)
        );
    }
}

<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Documento;
use App\Models\ArchivoEntrega;
use Illuminate\Support\Facades\Storage;

class DocumentoController extends Controller
{
    public function view(Documento $documento)
    {
        if ($documento->proyecto->cliente_id !== auth()->user()->cliente_id) {
            abort(403);
        }
        if (!$documento->visible_cliente) {
            abort(403);
        }
        if (!Storage::disk('local')->exists($documento->archivo_path)) {
            abort(404);
        }
        return response()->file(Storage::disk('local')->path($documento->archivo_path));
    }

    public function download(Documento $documento)
    {
        if ($documento->proyecto->cliente_id !== auth()->user()->cliente_id) {
            abort(403);
        }
        if (!$documento->visible_cliente) {
            abort(403);
        }
        if (!Storage::disk('local')->exists($documento->archivo_path)) {
            abort(404);
        }
        return Storage::disk('local')->download($documento->archivo_path, $documento->archivo_nombre_original);
    }

    public function viewArchivo(ArchivoEntrega $archivo)
    {
        if ($archivo->entrega->proyecto->cliente_id !== auth()->user()->cliente_id) {
            abort(403);
        }
        if ($archivo->es_video_url) {
            return redirect($archivo->video_url);
        }
        if (!Storage::disk('local')->exists($archivo->archivo_path)) {
            abort(404);
        }
        return response()->file(Storage::disk('local')->path($archivo->archivo_path));
    }
}

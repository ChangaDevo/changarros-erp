<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Documento;
use App\Models\ArchivoEntrega;
use App\Models\Proyecto;
use App\Models\ActividadLog;
use App\Services\NotificacionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentoController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'proyecto_id' => 'required|exists:proyectos,id',
            'nombre' => 'required|string|max:255',
            'tipo' => 'required|in:contrato,cotizacion,avance,entrega,otro',
            'archivo' => 'required|file|max:51200|mimes:pdf,jpg,jpeg,png,webp,gif,mp4,mov',
            'visible_cliente' => 'nullable|boolean',
            'notas' => 'nullable|string',
        ]);

        $proyecto = Proyecto::findOrFail($request->proyecto_id);
        $archivo = $request->file('archivo');
        $path = $archivo->store("proyectos/{$proyecto->id}/documentos", 'local');

        $documento = Documento::create([
            'proyecto_id' => $request->proyecto_id,
            'nombre' => $request->nombre,
            'tipo' => $request->tipo,
            'archivo_path' => $path,
            'archivo_nombre_original' => $archivo->getClientOriginalName(),
            'archivo_mime' => $archivo->getMimeType(),
            'archivo_tamanio' => $archivo->getSize(),
            'estado' => 'borrador',
            'visible_cliente' => $request->boolean('visible_cliente'),
            'notas' => $request->notas,
            'subido_por' => auth()->id(),
        ]);

        ActividadLog::registrar('subir_documento', "Documento subido: {$documento->nombre}", 'Documento', $documento->id);

        return redirect()->back()->with('success', 'Documento subido correctamente.');
    }

    public function enviar(Documento $documento)
    {
        if ($documento->es_sellado) {
            return back()->with('error', 'Este documento ya está sellado y no puede modificarse.');
        }
        $documento->update(['estado' => 'enviado', 'visible_cliente' => true]);
        ActividadLog::registrar('enviar_documento', "Documento enviado al cliente: {$documento->nombre}", 'Documento', $documento->id);

        NotificacionService::documentoEnviado($documento);

        return back()->with('success', 'Documento enviado al cliente.');
    }

    public function sellar(Documento $documento)
    {
        if ($documento->es_sellado) {
            return back()->with('error', 'Este documento ya está sellado.');
        }
        $documento->update([
            'estado' => 'sellado',
            'es_sellado' => true,
            'sellado_at' => now(),
            'sellado_por' => auth()->id(),
        ]);
        ActividadLog::registrar('sellar_documento', "Documento sellado: {$documento->nombre}", 'Documento', $documento->id);
        return back()->with('success', 'Documento sellado correctamente.');
    }

    public function destroy(Documento $documento)
    {
        if ($documento->es_sellado) {
            return back()->with('error', 'No se puede eliminar un documento sellado.');
        }
        Storage::disk('local')->delete($documento->archivo_path);
        ActividadLog::registrar('eliminar_documento', "Documento eliminado: {$documento->nombre}");
        $documento->delete();
        return back()->with('success', 'Documento eliminado.');
    }

    public function download(Documento $documento)
    {
        if (!Storage::disk('local')->exists($documento->archivo_path)) {
            abort(404);
        }
        return Storage::disk('local')->download($documento->archivo_path, $documento->archivo_nombre_original);
    }

    public function view(Documento $documento)
    {
        if (!Storage::disk('local')->exists($documento->archivo_path)) {
            abort(404);
        }
        return response()->file(Storage::disk('local')->path($documento->archivo_path));
    }

    public function viewArchivo(ArchivoEntrega $archivo)
    {
        if ($archivo->es_video_url) {
            return redirect($archivo->video_url);
        }
        if (!Storage::disk('local')->exists($archivo->archivo_path)) {
            abort(404);
        }
        return response()->file(Storage::disk('local')->path($archivo->archivo_path));
    }

    public function downloadArchivo(ArchivoEntrega $archivo)
    {
        if (!Storage::disk('local')->exists($archivo->archivo_path)) {
            abort(404);
        }
        return Storage::disk('local')->download($archivo->archivo_path, $archivo->archivo_nombre_original);
    }
}

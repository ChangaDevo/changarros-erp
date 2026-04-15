<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Entrega;
use App\Models\ArchivoEntrega;
use App\Models\Proyecto;
use App\Models\ActividadLog;
use App\Services\NotificacionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EntregaController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'proyecto_id' => 'required|exists:proyectos,id',
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'tipo' => 'required|in:diseno_inicial,avance,revision,entrega_final',
            'fecha_entrega' => 'nullable|date',
            'archivos.*' => 'nullable|file|max:102400',
            'video_url' => 'nullable|url',
        ]);

        $proyecto = Proyecto::findOrFail($request->proyecto_id);
        $orden = Entrega::where('proyecto_id', $proyecto->id)->max('orden') + 1;

        $entrega = Entrega::create([
            'proyecto_id' => $request->proyecto_id,
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'tipo' => $request->tipo,
            'estado' => 'enviado',
            'fecha_entrega' => $request->fecha_entrega,
            'orden' => $orden,
        ]);

        if ($request->hasFile('archivos')) {
            foreach ($request->file('archivos') as $archivo) {
                $mime = $archivo->getMimeType();
                $tipoArchivo = str_contains($mime, 'image') ? 'imagen' : (str_contains($mime, 'pdf') ? 'pdf' : (str_contains($mime, 'video') ? 'video_archivo' : 'otro'));
                $path = $archivo->store("proyectos/{$proyecto->id}/entregas/{$entrega->id}", 'local');
                ArchivoEntrega::create([
                    'entrega_id' => $entrega->id,
                    'nombre' => $archivo->getClientOriginalName(),
                    'archivo_path' => $path,
                    'archivo_nombre_original' => $archivo->getClientOriginalName(),
                    'tipo_archivo' => $tipoArchivo,
                    'archivo_tamanio' => $archivo->getSize(),
                ]);
            }
        }

        if ($request->filled('video_url')) {
            ArchivoEntrega::create([
                'entrega_id' => $entrega->id,
                'nombre' => 'Video enlace',
                'archivo_path' => '',
                'archivo_nombre_original' => 'video_url',
                'tipo_archivo' => 'video_url',
                'video_url' => $request->video_url,
            ]);
        }

        ActividadLog::registrar('crear_entrega', "Entrega creada: {$entrega->titulo}", 'Entrega', $entrega->id);

        NotificacionService::entregaEnviada($entrega);

        return redirect()->back()->with('success', 'Entrega enviada al cliente correctamente.');
    }

    public function destroy(Entrega $entrega)
    {
        if (in_array($entrega->estado, ['aprobado'])) {
            return back()->with('error', 'No se puede eliminar una entrega aprobada.');
        }
        foreach ($entrega->archivos as $archivo) {
            if ($archivo->archivo_path) {
                Storage::disk('local')->delete($archivo->archivo_path);
            }
        }
        ActividadLog::registrar('eliminar_entrega', "Entrega eliminada: {$entrega->titulo}");
        $entrega->delete();
        return back()->with('success', 'Entrega eliminada.');
    }
}

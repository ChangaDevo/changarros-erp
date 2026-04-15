<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Publicacion;
use App\Services\NotificacionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PublicacionController extends Controller
{
    private function clienteId(): int
    {
        return auth()->user()->cliente->id;
    }

    public function index()
    {
        return view('portal.publicaciones.index');
    }

    public function eventos(Request $request)
    {
        $clienteId = $this->clienteId();

        $query = Publicacion::where('cliente_id', $clienteId)
            ->where('estado', '!=', 'borrador');

        if ($request->filled('start') && $request->filled('end')) {
            $query->whereBetween('fecha_programada', [$request->start, $request->end]);
        }

        $iconos = [
            'instagram' => '📸',
            'facebook'  => '📘',
            'tiktok'    => '🎵',
            'twitter'   => '🐦',
            'linkedin'  => '💼',
            'youtube'   => '▶️',
        ];

        $eventos = $query->get()->map(function ($pub) use ($iconos) {
            $color = $pub->calendar_color;
            $icono = $iconos[$pub->red_social] ?? '📣';
            return [
                'id'              => $pub->id,
                'title'           => $icono . ' ' . $pub->titulo,
                'start'           => $pub->fecha_programada->toIso8601String(),
                'backgroundColor' => $color['bg'],
                'borderColor'     => $color['border'],
                'textColor'       => '#ffffff',
                'extendedProps'   => [
                    'estado'       => $pub->estado,
                    'estadoLabel'  => $pub->estado_label,
                    'estadoBadge'  => $pub->estado_badge,
                    'red_social'   => $pub->red_social,
                    'redLabel'     => $pub->red_social_label,
                    'descripcion'  => $pub->descripcion,
                    'imagen_url'   => $pub->archivo_path ? Storage::url($pub->archivo_path) : null,
                    'nota_cliente'       => $pub->nota_cliente,
                    'audiencia_sugerida' => $pub->audiencia_sugerida,
                ],
            ];
        });

        return response()->json($eventos);
    }

    public function aprobar(Request $request, Publicacion $publicacion)
    {
        abort_if($publicacion->cliente_id !== $this->clienteId(), 403);

        if ($publicacion->estado !== 'propuesto') {
            return response()->json(['error' => 'Solo se pueden aprobar publicaciones pendientes.'], 422);
        }

        $request->validate(['nota' => 'nullable|string|max:500']);

        $publicacion->update([
            'estado'       => 'aprobado',
            'nota_cliente' => $request->filled('nota') ? $request->nota : null,
        ]);

        NotificacionService::publicacionAprobada($publicacion);

        return response()->json(['ok' => true, 'mensaje' => 'Publicación aprobada.']);
    }

    public function rechazar(Request $request, Publicacion $publicacion)
    {
        abort_if($publicacion->cliente_id !== $this->clienteId(), 403);

        if ($publicacion->estado !== 'propuesto') {
            return response()->json(['error' => 'Solo se pueden rechazar publicaciones pendientes.'], 422);
        }

        $request->validate(['nota' => 'nullable|string|max:500']);

        $publicacion->update([
            'estado'       => 'rechazado',
            'nota_cliente' => $request->nota,
        ]);

        NotificacionService::publicacionRechazada($publicacion);

        return response()->json(['ok' => true, 'mensaje' => 'Publicación rechazada.']);
    }
}

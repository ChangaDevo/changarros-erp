<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Publicacion;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class PublicacionController extends Controller
{
    public function index()
    {
        $clientes = Cliente::orderBy('nombre_empresa')->get();
        // Pasar mapa de dias_minimos por cliente para el frontend
        $clientes_minimos = $clientes->mapWithKeys(fn($c) => [
            $c->id => [
                'dias' => $c->dias_minimos_publicacion ?? 2,
                'interno' => (bool) $c->es_cliente_interno,
            ]
        ]);
        return view('admin.publicaciones.index', compact('clientes', 'clientes_minimos'));
    }

    public function eventos(Request $request)
    {
        $query = Publicacion::with(['cliente', 'proyecto']);

        if ($request->filled('start') && $request->filled('end')) {
            $query->whereBetween('fecha_programada', [$request->start, $request->end]);
        }
        if ($request->filled('cliente_id')) {
            $query->where('cliente_id', $request->cliente_id);
        }
        if ($request->filled('red_social')) {
            $query->where('red_social', $request->red_social);
        }
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
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
                'title'           => $icono . ' ' . $pub->titulo . ' — ' . $pub->cliente->nombre_empresa,
                'start'           => $pub->fecha_programada->toIso8601String(),
                'backgroundColor' => $color['bg'],
                'borderColor'     => $color['border'],
                'textColor'       => '#ffffff',
                'extendedProps'   => [
                    'estado'            => $pub->estado,
                    'estadoLabel'       => $pub->estado_label,
                    'estadoBadge'       => $pub->estado_badge,
                    'red_social'        => $pub->red_social,
                    'redLabel'          => $pub->red_social_label,
                    'cliente'           => $pub->cliente->nombre_empresa,
                    'cliente_id'        => $pub->cliente_id,
                    'proyecto_id'       => $pub->proyecto_id,
                    'proyecto'          => $pub->proyecto?->nombre,
                    'descripcion'       => $pub->descripcion,
                    'archivo_url'       => $pub->archivo_path ? Storage::url($pub->archivo_path) : null,
                    'nota_cliente'      => $pub->nota_cliente,
                    'audiencia_sugerida'=> $pub->audiencia_sugerida,
                ],
            ];
        });

        return response()->json($eventos);
    }

    // -------------------------------------------------------
    // Análisis con IA (Claude)
    // -------------------------------------------------------
    public function analizarImagen(Request $request)
    {
        $request->validate([
            'archivo'    => 'required|file|max:10240',
            'red_social' => 'required|string',
            'cliente_id' => 'nullable|exists:clientes,id',
        ]);

        $file     = $request->file('archivo');
        $mimeType = $file->getMimeType();
        $isVideo  = str_starts_with($mimeType, 'video/');

        // Para video solo mandamos metadata (Claude no procesa video directamente)
        if ($isVideo) {
            $prompt = $this->buildVideoPrompt($request->red_social, $request->cliente_id, $file->getClientOriginalName());
            $messages = [['role' => 'user', 'content' => $prompt]];
        } else {
            $base64  = base64_encode(file_get_contents($file->getRealPath()));
            $messages = [[
                'role'    => 'user',
                'content' => [
                    [
                        'type'   => 'image',
                        'source' => [
                            'type'       => 'base64',
                            'media_type' => $mimeType,
                            'data'       => $base64,
                        ],
                    ],
                    [
                        'type' => 'text',
                        'text' => $this->buildImagePrompt($request->red_social, $request->cliente_id),
                    ],
                ],
            ]];
        }

        $response = Http::withHeaders([
            'x-api-key'         => config('services.anthropic.key'),
            'anthropic-version' => '2023-06-01',
            'content-type'      => 'application/json',
        ])->timeout(30)->post('https://api.anthropic.com/v1/messages', [
            'model'      => config('services.anthropic.model'),
            'max_tokens' => 1024,
            'messages'   => $messages,
        ]);

        if ($response->failed()) {
            return response()->json(['error' => 'Error al contactar la IA: ' . $response->body()], 500);
        }

        $text = $response->json('content.0.text', '');

        // Extraer JSON de la respuesta
        preg_match('/\{.*\}/s', $text, $matches);
        if (empty($matches[0])) {
            return response()->json(['error' => 'La IA no devolvió un formato válido.', 'raw' => $text], 500);
        }

        $data = json_decode($matches[0], true);
        if (!$data) {
            return response()->json(['error' => 'No se pudo parsear la respuesta de la IA.', 'raw' => $text], 500);
        }

        return response()->json([
            'ok'               => true,
            'copy'             => $data['copy']              ?? '',
            'hora_sugerida'    => $data['hora_sugerida']     ?? '18:00',
            'audiencia'        => $data['audiencia']         ?? '',
            'tipo_contenido'   => $data['tipo_contenido']    ?? '',
            'justificacion'    => $data['justificacion']     ?? '',
        ]);
    }

    private function buildImagePrompt(string $redSocial, ?int $clienteId): string
    {
        $clienteInfo = '';
        if ($clienteId) {
            $cliente = Cliente::find($clienteId);
            if ($cliente) {
                $clienteInfo = "El cliente es \"{$cliente->nombre_empresa}\".";
                if (!empty($cliente->giro)) {
                    $clienteInfo .= " Su giro/industria es: {$cliente->giro}.";
                }
            }
        }

        $redLabels = [
            'instagram' => 'Instagram',
            'facebook'  => 'Facebook',
            'tiktok'    => 'TikTok',
            'twitter'   => 'Twitter / X',
            'linkedin'  => 'LinkedIn',
            'youtube'   => 'YouTube',
        ];
        $redLabel = $redLabels[$redSocial] ?? $redSocial;

        return <<<PROMPT
Eres un experto en marketing digital y redes sociales para el mercado latinoamericano (principalmente México).
{$clienteInfo}
Analiza esta imagen que se publicará en {$redLabel}.

Responde ÚNICAMENTE con un JSON con esta estructura (sin texto adicional):
{
  "copy": "texto del post optimizado para {$redLabel}, con emojis adecuados y hashtags relevantes",
  "hora_sugerida": "HH:MM en formato 24h, la hora óptima de publicación para mayor alcance en {$redLabel} para audiencia mexicana/latinoamericana",
  "audiencia": "descripción detallada del público objetivo sugerido para publicidad pagada (edad, género, intereses, comportamientos, ubicación)",
  "tipo_contenido": "clasificación breve del contenido detectado (ej: Producto lifestyle, Promoción, Servicio, Evento, etc.)",
  "justificacion": "explicación breve (2-3 oraciones) de por qué sugieres esa hora y esa audiencia"
}
PROMPT;
    }

    private function buildVideoPrompt(string $redSocial, ?int $clienteId, string $filename): string
    {
        $clienteInfo = '';
        if ($clienteId) {
            $cliente = Cliente::find($clienteId);
            if ($cliente) {
                $clienteInfo = "El cliente es \"{$cliente->nombre_empresa}\".";
                if (!empty($cliente->giro)) {
                    $clienteInfo .= " Su giro/industria es: {$cliente->giro}.";
                }
            }
        }

        $redLabels = [
            'instagram' => 'Instagram',
            'facebook'  => 'Facebook',
            'tiktok'    => 'TikTok',
            'twitter'   => 'Twitter / X',
            'linkedin'  => 'LinkedIn',
            'youtube'   => 'YouTube',
        ];
        $redLabel = $redLabels[$redSocial] ?? $redSocial;

        return <<<PROMPT
Eres un experto en marketing digital y redes sociales para el mercado latinoamericano (principalmente México).
{$clienteInfo}
Se va a publicar un video (archivo: {$filename}) en {$redLabel}.

Basándote en el tipo de plataforma y el contexto del cliente, genera sugerencias de marketing.

Responde ÚNICAMENTE con un JSON con esta estructura (sin texto adicional):
{
  "copy": "texto del post optimizado para {$redLabel} para acompañar al video, con emojis adecuados y hashtags relevantes",
  "hora_sugerida": "HH:MM en formato 24h, la hora óptima de publicación para mayor alcance en {$redLabel} para audiencia mexicana/latinoamericana",
  "audiencia": "descripción detallada del público objetivo sugerido para publicidad pagada (edad, género, intereses, comportamientos, ubicación)",
  "tipo_contenido": "Video para {$redLabel}",
  "justificacion": "explicación breve (2-3 oraciones) de por qué sugieres esa hora y esa audiencia para videos en {$redLabel}"
}
PROMPT;
    }

    // -------------------------------------------------------
    // CRUD
    // -------------------------------------------------------
    private function validarFechaMinima(Request $request): void
    {
        $clienteId = $request->input('cliente_id');
        if (!$clienteId) return;

        $cliente = Cliente::find($clienteId);
        if (!$cliente) return;

        $dias = (int) ($cliente->dias_minimos_publicacion ?? 2);
        if ($dias > 0) {
            $minFecha = now()->addDays($dias)->startOfDay();
            $fechaProgramada = \Carbon\Carbon::parse($request->input('fecha_programada'));
            if ($fechaProgramada->lt($minFecha)) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'fecha_programada' => "La fecha mínima de publicación para este cliente es {$minFecha->format('d/m/Y')} ({$dias} día(s) de anticipación).",
                ]);
            }
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cliente_id'         => 'required|exists:clientes,id',
            'proyecto_id'        => 'nullable|exists:proyectos,id',
            'red_social'         => 'required|in:instagram,facebook,tiktok,twitter,linkedin,youtube',
            'titulo'             => 'required|string|max:255',
            'descripcion'        => 'required|string',
            'fecha_programada'   => 'required|date',
            'estado'             => 'required|in:borrador,propuesto',
            'archivo'            => 'nullable|file|mimes:jpeg,jpg,png,gif,webp,mp4,mov,webm|max:51200',
            'audiencia_sugerida' => 'nullable|string',
        ]);

        $this->validarFechaMinima($request);

        if ($request->hasFile('archivo')) {
            $validated['archivo_path'] = $request->file('archivo')->store('publicaciones', 'public');
        }
        unset($validated['archivo']);
        $validated['created_by'] = auth()->id();

        Publicacion::create($validated);

        return response()->json(['ok' => true]);
    }

    public function show(Publicacion $publicacion)
    {
        $publicacion->load(['cliente', 'proyecto']);
        return response()->json([
            'id'                 => $publicacion->id,
            'cliente_id'         => $publicacion->cliente_id,
            'proyecto_id'        => $publicacion->proyecto_id,
            'red_social'         => $publicacion->red_social,
            'titulo'             => $publicacion->titulo,
            'descripcion'        => $publicacion->descripcion,
            'fecha_programada'   => $publicacion->fecha_programada->format('Y-m-d\TH:i'),
            'estado'             => $publicacion->estado,
            'estado_label'       => $publicacion->estado_label,
            'estado_badge'       => $publicacion->estado_badge,
            'nota_cliente'       => $publicacion->nota_cliente,
            'audiencia_sugerida' => $publicacion->audiencia_sugerida,
            'archivo_url'        => $publicacion->archivo_path ? Storage::url($publicacion->archivo_path) : null,
            'cliente_nombre'     => $publicacion->cliente->nombre_empresa,
            'proyecto_nombre'    => $publicacion->proyecto?->nombre,
        ]);
    }

    public function update(Request $request, Publicacion $publicacion)
    {
        $validated = $request->validate([
            'cliente_id'         => 'required|exists:clientes,id',
            'proyecto_id'        => 'nullable|exists:proyectos,id',
            'red_social'         => 'required|in:instagram,facebook,tiktok,twitter,linkedin,youtube',
            'titulo'             => 'required|string|max:255',
            'descripcion'        => 'required|string',
            'fecha_programada'   => 'required|date',
            'estado'             => 'required|in:borrador,propuesto,aprobado,rechazado,publicado',
            'archivo'            => 'nullable|file|mimes:jpeg,jpg,png,gif,webp,mp4,mov,webm|max:51200',
            'audiencia_sugerida' => 'nullable|string',
        ]);

        // Solo validar mínimo si la fecha cambió
        if ($request->input('fecha_programada') !== $publicacion->fecha_programada->format('Y-m-d\TH:i')) {
            $this->validarFechaMinima($request);
        }

        if ($request->hasFile('archivo')) {
            if ($publicacion->archivo_path) {
                Storage::disk('public')->delete($publicacion->archivo_path);
            }
            $validated['archivo_path'] = $request->file('archivo')->store('publicaciones', 'public');
        }
        unset($validated['archivo']);

        $publicacion->update($validated);

        return response()->json(['ok' => true]);
    }

    public function publicar(Publicacion $publicacion)
    {
        $publicacion->update(['estado' => 'publicado']);
        return response()->json(['ok' => true]);
    }

    public function destroy(Publicacion $publicacion)
    {
        if ($publicacion->archivo_path) {
            Storage::disk('public')->delete($publicacion->archivo_path);
        }
        $publicacion->delete();
        return response()->json(['ok' => true]);
    }
}

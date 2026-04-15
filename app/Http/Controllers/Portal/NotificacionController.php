<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Notificacion;
use Illuminate\Http\Request;

class NotificacionController extends Controller
{
    private function clienteId(): int
    {
        return auth()->user()->cliente_id;
    }

    public function index()
    {
        $notificaciones = Notificacion::where('cliente_id', $this->clienteId())
            ->latest()
            ->paginate(30);

        Notificacion::where('cliente_id', $this->clienteId())
            ->whereNull('leida_at')
            ->update(['leida_at' => now()]);

        return view('portal.notificaciones.index', compact('notificaciones'));
    }

    public function recent()
    {
        $items = Notificacion::where('cliente_id', $this->clienteId())
            ->whereNull('leida_at')
            ->latest()
            ->limit(10)
            ->get()
            ->map(fn($n) => [
                'id'      => $n->id,
                'titulo'  => $n->titulo,
                'mensaje' => $n->mensaje,
                'url'     => $n->url,
                'icono'   => $n->tipo_icono,
                'color'   => $n->tipo_color,
                'tiempo'  => $n->created_at->diffForHumans(),
            ]);

        $total = Notificacion::where('cliente_id', $this->clienteId())
            ->whereNull('leida_at')->count();

        return response()->json(['items' => $items, 'total' => $total]);
    }

    public function markRead(Notificacion $notificacion)
    {
        if ($notificacion->cliente_id !== $this->clienteId()) abort(403);
        $notificacion->marcarLeida();
        return response()->json(['ok' => true]);
    }

    public function markAllRead()
    {
        Notificacion::where('cliente_id', $this->clienteId())
            ->whereNull('leida_at')
            ->update(['leida_at' => now()]);
        return response()->json(['ok' => true]);
    }
}

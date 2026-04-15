<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notificacion;
use Illuminate\Http\Request;

class NotificacionController extends Controller
{
    public function index()
    {
        $notificaciones = Notificacion::where('user_id', auth()->id())
            ->latest()
            ->paginate(30);

        // Marcar todas como leídas al ver la página
        Notificacion::where('user_id', auth()->id())
            ->whereNull('leida_at')
            ->update(['leida_at' => now()]);

        return view('admin.notificaciones.index', compact('notificaciones'));
    }

    public function recent()
    {
        $items = Notificacion::where('user_id', auth()->id())
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

        $total_no_leidas = Notificacion::where('user_id', auth()->id())
            ->whereNull('leida_at')->count();

        return response()->json(['items' => $items, 'total' => $total_no_leidas]);
    }

    public function markRead(Notificacion $notificacion)
    {
        if ($notificacion->user_id !== auth()->id()) abort(403);
        $notificacion->marcarLeida();
        return response()->json(['ok' => true]);
    }

    public function markAllRead()
    {
        Notificacion::where('user_id', auth()->id())
            ->whereNull('leida_at')
            ->update(['leida_at' => now()]);
        return response()->json(['ok' => true]);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\EnviarCampanaJob;
use App\Models\CampanaEmail;
use App\Models\CampanaContacto;
use App\Models\Cliente;
use Illuminate\Http\Request;

class CampanaEmailController extends Controller
{
    public function index()
    {
        $campanas = CampanaEmail::with('cliente')
            ->latest()
            ->paginate(15);

        return view('admin.mailing.index', compact('campanas'));
    }

    public function create()
    {
        $clientes = Cliente::where('activo', true)->orderBy('nombre_empresa')->get();
        return view('admin.mailing.create', compact('clientes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cliente_id'       => 'required|exists:clientes,id',
            'titulo'           => 'required|string|max:255',
            'asunto'           => 'required|string|max:255',
            'remitente_nombre' => 'required|string|max:255',
            'remitente_email'  => 'required|email|max:255',
            'cuerpo_html'      => 'required|string',
            'csv'              => 'required|file|mimes:csv,txt|max:5120',
        ]);

        $campana = CampanaEmail::create([
            'cliente_id'       => $validated['cliente_id'],
            'creado_por'       => auth()->id(),
            'titulo'           => $validated['titulo'],
            'asunto'           => $validated['asunto'],
            'remitente_nombre' => $validated['remitente_nombre'],
            'remitente_email'  => $validated['remitente_email'],
            'cuerpo_html'      => $validated['cuerpo_html'],
        ]);

        $total = $this->importarCsv($campana, $request->file('csv'));
        $campana->update(['total_contactos' => $total]);

        return redirect()->route('admin.mailing.show', $campana)
            ->with('success', "Campaña creada con {$total} contactos.");
    }

    public function show(CampanaEmail $mailing)
    {
        $mailing->load('cliente', 'creadoPor');
        $contactos = $mailing->contactos()->latest()->paginate(20);
        $primerContacto = $mailing->contactos()->first();

        return view('admin.mailing.show', compact('mailing', 'contactos', 'primerContacto'));
    }

    public function edit(CampanaEmail $mailing)
    {
        $clientes = Cliente::where('activo', true)->orderBy('nombre_empresa')->get();
        return view('admin.mailing.edit', compact('mailing', 'clientes'));
    }

    public function update(Request $request, CampanaEmail $mailing)
    {
        if ($mailing->estado === 'enviada') {
            return back()->with('error', 'No puedes editar una campaña ya enviada.');
        }

        $validated = $request->validate([
            'titulo'           => 'required|string|max:255',
            'asunto'           => 'required|string|max:255',
            'remitente_nombre' => 'required|string|max:255',
            'remitente_email'  => 'required|email|max:255',
            'cuerpo_html'      => 'required|string',
            'csv'              => 'nullable|file|mimes:csv,txt|max:5120',
        ]);

        $mailing->update($validated);

        // Si sube nuevo CSV, reemplazar contactos
        if ($request->hasFile('csv')) {
            $mailing->contactos()->delete();
            $total = $this->importarCsv($mailing, $request->file('csv'));
            $mailing->update([
                'total_contactos' => $total,
                'total_enviados'  => 0,
                'total_errores'   => 0,
            ]);
        }

        return redirect()->route('admin.mailing.show', $mailing)
            ->with('success', 'Campaña actualizada.');
    }

    public function destroy(CampanaEmail $mailing)
    {
        $mailing->delete();
        return redirect()->route('admin.mailing.index')
            ->with('success', 'Campaña eliminada.');
    }

    /**
     * Preview del HTML renderizado con el primer contacto (o datos de ejemplo)
     */
    public function preview(CampanaEmail $mailing)
    {
        $contacto = $mailing->contactos()->first()
            ?? new CampanaContacto([
                'nombre'   => 'Juan',
                'apellido' => 'Pérez',
                'email'    => 'juan@ejemplo.com',
                'empresa'  => 'Empresa Ejemplo',
            ]);

        return response($mailing->renderizar($contacto))
            ->header('Content-Type', 'text/html');
    }

    /**
     * Preview en vivo desde el formulario (sin guardar)
     */
    public function previewLive(Request $request)
    {
        $html = $request->input('cuerpo_html', '');

        $vars = [
            '{nombre}'          => 'Juan',
            '{apellido}'        => 'Pérez',
            '{empresa}'         => 'Empresa Ejemplo',
            '{email}'           => 'juan@ejemplo.com',
            '{nombre_completo}' => 'Juan Pérez',
        ];

        return response(str_replace(array_keys($vars), array_values($vars), $html))
            ->header('Content-Type', 'text/html');
    }

    /**
     * Disparar el envío masivo
     */
    public function enviar(CampanaEmail $mailing)
    {
        if ($mailing->estado === 'enviada') {
            return back()->with('error', 'Esta campaña ya fue enviada.');
        }

        if ($mailing->total_contactos === 0) {
            return back()->with('error', 'No hay contactos en esta campaña.');
        }

        $mailing->update(['estado' => 'enviando']);

        $esSync = config('queue.default') === 'sync';

        // Con sync: enviar directo sin Jobs para evitar que quede en "enviando"
        if ($esSync) {
            $enviados = 0;
            $errores  = 0;

            $mailing->contactos()->where('estado', 'pendiente')->each(function ($contacto) use ($mailing, &$enviados, &$errores) {
                try {
                    \Mail::to($contacto->email)
                        ->send(new \App\Mail\BoletinMail($mailing, $contacto));

                    $contacto->update(['estado' => 'enviado', 'enviado_at' => now()]);
                    $enviados++;
                } catch (\Throwable $e) {
                    $contacto->update(['estado' => 'error', 'error_mensaje' => $e->getMessage()]);
                    $errores++;
                }
            });

            $mailing->update([
                'estado'          => 'enviada',
                'enviado_at'      => now(),
                'total_enviados'  => $enviados,
                'total_errores'   => $errores,
            ]);

            return redirect()->route('admin.mailing.show', $mailing)
                ->with('success', "✅ Campaña enviada: {$enviados} correos enviados" . ($errores ? ", {$errores} errores." : "."));
        }

        // Con queue (database/redis): dispatch jobs en chunks
        $mailing->contactos()
            ->where('estado', 'pendiente')
            ->chunk(50, function ($contactos) use ($mailing) {
                foreach ($contactos as $contacto) {
                    \App\Jobs\EnviarCampanaJob::dispatch($mailing, $contacto)
                        ->onQueue('mailing');
                }
            });

        return back()->with('success', "Envío en cola iniciado para {$mailing->total_contactos} contactos.");
    }

    // ── Helpers ───────────────────────────────────────────────

    private function importarCsv(CampanaEmail $campana, $archivo): int
    {
        $ruta  = $archivo->getRealPath();
        $handle = fopen($ruta, 'r');
        $headers = null;
        $count  = 0;

        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
            // Primera fila = encabezados
            if ($headers === null) {
                $headers = array_map('strtolower', array_map('trim', $row));
                continue;
            }

            $data = array_combine($headers, $row);

            // Campos estándar
            $email = trim($data['email'] ?? $data['correo'] ?? '');
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) continue;

            $nombre   = trim($data['nombre'] ?? '');
            $apellido = trim($data['apellido'] ?? $data['apellidos'] ?? '');
            $empresa  = trim($data['empresa'] ?? $data['company'] ?? '');

            // Todo lo demás va a datos_extra
            $extra = array_diff_key($data, array_flip(['email','correo','nombre','apellido','apellidos','empresa','company']));

            CampanaContacto::create([
                'campana_id'  => $campana->id,
                'nombre'      => $nombre ?: 'Sin nombre',
                'apellido'    => $apellido ?: null,
                'email'       => $email,
                'empresa'     => $empresa ?: null,
                'datos_extra' => !empty($extra) ? $extra : null,
            ]);

            $count++;
        }

        fclose($handle);
        return $count;
    }
}

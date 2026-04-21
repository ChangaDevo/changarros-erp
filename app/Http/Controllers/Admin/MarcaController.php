<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Marca;
use App\Models\MarcaRecurso;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class MarcaController extends Controller
{
    public function index()
    {
        $marcas = Marca::with('cliente')
            ->withCount('recursos')
            ->latest()->paginate(12);

        return view('admin.marcas.index', compact('marcas'));
    }

    public function create()
    {
        $clientes = Cliente::where('activo', true)->orderBy('nombre_empresa')->get();
        return view('admin.marcas.create', compact('clientes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cliente_id'  => 'required|exists:clientes,id',
            'nombre'      => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:1000',
            'tagline'     => 'nullable|string|max:255',
            'sitio_web'   => 'nullable|url|max:255',
            'industria'   => 'nullable|string|max:100',
        ]);

        $marca = Marca::create(array_merge($validated, ['creado_por' => auth()->id()]));

        return redirect()->route('admin.marcas.show', $marca)
            ->with('success', "Marca \"{$marca->nombre}\" creada. Ahora sube los recursos.");
    }

    public function show(Marca $marca)
    {
        $marca->load('cliente', 'creadoPor');
        $logos       = $marca->logos()->get();
        $tipografias = $marca->tipografias()->get();
        $colores     = $marca->colores()->get();
        $templates   = $marca->templates()->get();
        $otros       = $marca->otros()->get();

        return view('admin.marcas.show', compact(
            'marca', 'logos', 'tipografias', 'colores', 'templates', 'otros'
        ));
    }

    public function edit(Marca $marca)
    {
        $clientes = Cliente::where('activo', true)->orderBy('nombre_empresa')->get();
        return view('admin.marcas.edit', compact('marca', 'clientes'));
    }

    public function update(Request $request, Marca $marca)
    {
        $validated = $request->validate([
            'nombre'      => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:1000',
            'tagline'     => 'nullable|string|max:255',
            'sitio_web'   => 'nullable|url|max:255',
            'industria'   => 'nullable|string|max:100',
        ]);

        $marca->update($validated);

        return redirect()->route('admin.marcas.show', $marca)
            ->with('success', 'Marca actualizada.');
    }

    public function destroy(Marca $marca)
    {
        // Borrar archivos del storage
        foreach ($marca->recursos as $recurso) {
            if ($recurso->archivo_path) {
                Storage::disk('public')->delete($recurso->archivo_path);
            }
        }
        $marca->delete();

        return redirect()->route('admin.marcas.index')
            ->with('success', 'Marca eliminada.');
    }

    // ── Toggle acceso cliente ─────────────────────────────────

    public function toggleAcceso(Marca $marca)
    {
        $marca->update(['acceso_cliente' => !$marca->acceso_cliente]);
        $estado = $marca->acceso_cliente ? 'activado' : 'desactivado';

        return back()->with('success', "Acceso del cliente {$estado}.");
    }

    // ── Subir recurso ─────────────────────────────────────────

    public function subirRecurso(Request $request, Marca $marca)
    {
        $request->validate([
            'tipo'     => 'required|in:logo,tipografia,color,template,otro',
            'nombre'   => 'required|string|max:255',
            'variante' => 'nullable|string|max:100',
            'descripcion' => 'nullable|string|max:500',
            // Archivo (requerido para todo excepto colores)
            'archivo'  => 'nullable|file|max:51200|mimes:png,jpg,jpeg,svg,gif,webp,pdf,ai,eps,psd,ttf,otf,woff,woff2,zip,docx,xlsx',
            // Solo colores
            'color_hex'    => 'nullable|string|max:7',
            'color_nombre' => 'nullable|string|max:100',
        ]);

        $data = [
            'marca_id'    => $marca->id,
            'tipo'        => $request->tipo,
            'nombre'      => $request->nombre,
            'descripcion' => $request->descripcion,
            'variante'    => $request->variante,
            'color_hex'   => $request->color_hex,
            'color_nombre'=> $request->color_nombre,
            'orden'       => $marca->recursos()->max('orden') + 1,
        ];

        if ($request->hasFile('archivo')) {
            $file = $request->file('archivo');
            $data['archivo_path']            = $file->store("marcas/{$marca->id}/{$request->tipo}", 'public');
            $data['archivo_nombre_original'] = $file->getClientOriginalName();
            $data['archivo_mime']            = $file->getMimeType();
            $data['archivo_tamanio']         = $file->getSize();
        }

        MarcaRecurso::create($data);

        return back()->with('success', 'Recurso subido correctamente.');
    }

    // ── Eliminar recurso ──────────────────────────────────────

    public function eliminarRecurso(Marca $marca, MarcaRecurso $recurso)
    {
        if ($recurso->archivo_path) {
            Storage::disk('public')->delete($recurso->archivo_path);
        }
        $recurso->delete();

        return back()->with('success', 'Recurso eliminado.');
    }

    // ── Descargar recurso ─────────────────────────────────────

    public function descargarRecurso(Marca $marca, MarcaRecurso $recurso)
    {
        abort_unless($recurso->archivo_path, 404);
        return Storage::disk('public')->download(
            $recurso->archivo_path,
            $recurso->archivo_nombre_original ?? basename($recurso->archivo_path)
        );
    }

    // ── Exportar ZIP ──────────────────────────────────────────

    public function exportarZip(Marca $marca)
    {
        $recursos = $marca->recursos()->whereNotNull('archivo_path')->get();

        if ($recursos->isEmpty()) {
            return back()->with('error', 'No hay archivos para exportar.');
        }

        $zipNombre = 'marca_' . \Str::slug($marca->nombre) . '_' . now()->format('Ymd') . '.zip';
        $zipPath   = storage_path('app/temp/' . $zipNombre);

        // Crear carpeta temp si no existe
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $zip = new ZipArchive();
        $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        // Organizar por carpetas de tipo
        $tipos = ['logo' => 'Logos', 'tipografia' => 'Tipografias', 'template' => 'Templates', 'otro' => 'Otros'];
        foreach ($recursos as $recurso) {
            $carpeta = $tipos[$recurso->tipo] ?? 'Otros';
            $nombreArchivo = $carpeta . '/' . ($recurso->archivo_nombre_original ?? basename($recurso->archivo_path));
            $rutaReal = Storage::disk('public')->path($recurso->archivo_path);
            if (file_exists($rutaReal)) {
                $zip->addFile($rutaReal, $nombreArchivo);
            }
        }

        // Agregar paleta de colores como txt
        $colores = $marca->colores()->get();
        if ($colores->isNotEmpty()) {
            $txt = "PALETA DE COLORES — {$marca->nombre}\n";
            $txt .= str_repeat('=', 40) . "\n\n";
            foreach ($colores as $c) {
                $txt .= "{$c->nombre}: {$c->color_hex}\n";
                if ($c->descripcion) $txt .= "  {$c->descripcion}\n";
            }
            $zip->addFromString('Colores/paleta.txt', $txt);
        }

        $zip->close();

        return response()->download($zipPath, $zipNombre)->deleteFileAfterSend(true);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\MetaToken;
use App\Services\MetaPublicacionService;
use Illuminate\Http\Request;

class MetaTokenController extends Controller
{
    public function index()
    {
        $tokens  = MetaToken::with('cliente')->orderBy('cliente_id')->orderBy('plataforma')->get();
        $clientes = Cliente::orderBy('nombre_empresa')->get();
        return view('admin.meta-tokens.index', compact('tokens', 'clientes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cliente_id'    => 'required|exists:clientes,id',
            'nombre'        => 'required|string|max:100',
            'plataforma'    => 'required|in:facebook,instagram',
            'page_id'       => 'required|string|max:50',
            'ig_account_id' => 'nullable|string|max:50',
            'access_token'  => 'required|string',
            'activo'        => 'boolean',
            'expires_at'    => 'nullable|date',
        ]);

        $validated['activo'] = $request->boolean('activo', true);

        MetaToken::create($validated);

        return response()->json(['ok' => true]);
    }

    public function show(MetaToken $metaToken)
    {
        return response()->json([
            'id'            => $metaToken->id,
            'cliente_id'    => $metaToken->cliente_id,
            'nombre'        => $metaToken->nombre,
            'plataforma'    => $metaToken->plataforma,
            'page_id'       => $metaToken->page_id,
            'ig_account_id' => $metaToken->ig_account_id,
            'access_token'  => $metaToken->access_token, // descifrado solo al admin
            'activo'        => $metaToken->activo,
            'expires_at'    => $metaToken->expires_at?->format('Y-m-d'),
            'estado_verificacion'  => $metaToken->estado_verificacion,
            'ultima_verificacion'  => $metaToken->ultima_verificacion?->diffForHumans(),
        ]);
    }

    public function update(Request $request, MetaToken $metaToken)
    {
        $validated = $request->validate([
            'cliente_id'    => 'required|exists:clientes,id',
            'nombre'        => 'required|string|max:100',
            'plataforma'    => 'required|in:facebook,instagram',
            'page_id'       => 'required|string|max:50',
            'ig_account_id' => 'nullable|string|max:50',
            'access_token'  => 'required|string',
            'activo'        => 'boolean',
            'expires_at'    => 'nullable|date',
        ]);

        $validated['activo'] = $request->boolean('activo', true);

        $metaToken->update($validated);

        return response()->json(['ok' => true]);
    }

    public function destroy(MetaToken $metaToken)
    {
        $metaToken->delete();
        return response()->json(['ok' => true]);
    }

    public function verificar(MetaToken $metaToken, MetaPublicacionService $meta)
    {
        $resultado = $meta->verificarToken($metaToken);

        $metaToken->update([
            'ultima_verificacion' => now(),
            'estado_verificacion' => $resultado['ok'] ? 'ok' : 'error',
        ]);

        return response()->json($resultado);
    }

    public function detectarIg(Request $request, MetaPublicacionService $meta)
    {
        $request->validate([
            'page_id'      => 'required|string',
            'access_token' => 'required|string',
        ]);

        $resultado = $meta->detectarIgAccountId($request->page_id, $request->access_token);

        return response()->json($resultado);
    }
}

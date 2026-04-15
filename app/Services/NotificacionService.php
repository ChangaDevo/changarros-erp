<?php

namespace App\Services;

use App\Models\Notificacion;
use App\Models\User;
use App\Models\Entrega;
use App\Models\Documento;
use App\Models\Publicacion;
use App\Mail\EntregaEnviada;
use App\Mail\EntregaAprobada;
use App\Mail\EntregaRechazada;
use App\Mail\DocumentoEnviado;
use App\Mail\DocumentoAprobado;
use App\Mail\CotizacionEstadoCambiado;
use Illuminate\Support\Facades\Mail;

class NotificacionService
{
    // ── Utilidades ────────────────────────────────────────────────

    private static function adminsDelProyecto($proyecto): \Illuminate\Support\Collection
    {
        $ids = collect([$proyecto->creado_por])
            ->merge($proyecto->usuariosCompartidos->pluck('id'))
            ->unique()
            ->filter();

        return User::whereIn('id', $ids)->where('activo', 1)->get();
    }

    private static function todosLosAdmins(): \Illuminate\Support\Collection
    {
        return User::whereIn('role', ['admin', 'superadmin'])->where('activo', 1)->get();
    }

    // ── Entregas ──────────────────────────────────────────────────

    /**
     * Admin envía entrega al cliente → notificar al cliente
     */
    public static function entregaEnviada(Entrega $entrega): void
    {
        $proyecto = $entrega->proyecto;
        if (!$proyecto) return;

        $cliente = $proyecto->cliente;
        if (!$cliente) return;

        Notificacion::paraCliente(
            $cliente,
            'entrega_enviada',
            'Nueva entrega para revisar',
            route('portal.proyectos.show', $proyecto->id),
            $entrega,
            "Se ha subido una nueva entrega: \"{$entrega->titulo}\" en el proyecto {$proyecto->nombre}."
        );

        if ($cliente->email) {
            Mail::to($cliente->email)->queue(new EntregaEnviada($entrega));
        }
    }

    /**
     * Cliente aprueba entrega → notificar admins del proyecto
     */
    public static function entregaAprobada(Entrega $entrega): void
    {
        $proyecto = $entrega->proyecto->load('usuariosCompartidos');
        $admins   = self::adminsDelProyecto($proyecto);

        foreach ($admins as $admin) {
            Notificacion::paraAdmin(
                $admin,
                'entrega_aprobada',
                'Entrega aprobada por el cliente',
                route('admin.proyectos.show', $proyecto->id),
                $entrega,
                "El cliente aprobó la entrega \"{$entrega->titulo}\" del proyecto {$proyecto->nombre}."
            );
            if ($admin->email) {
                Mail::to($admin->email)->queue(new EntregaAprobada($entrega));
            }
        }
    }

    /**
     * Cliente solicita cambios → notificar admins del proyecto
     */
    public static function entregaRechazada(Entrega $entrega): void
    {
        $proyecto = $entrega->proyecto->load('usuariosCompartidos');
        $admins   = self::adminsDelProyecto($proyecto);

        foreach ($admins as $admin) {
            Notificacion::paraAdmin(
                $admin,
                'entrega_rechazada',
                'El cliente solicitó cambios',
                route('admin.proyectos.show', $proyecto->id),
                $entrega,
                "Se solicitaron cambios en la entrega \"{$entrega->titulo}\" del proyecto {$proyecto->nombre}."
            );
            if ($admin->email) {
                Mail::to($admin->email)->queue(new EntregaRechazada($entrega));
            }
        }
    }

    // ── Documentos ────────────────────────────────────────────────

    /**
     * Admin envía documento al cliente → notificar cliente
     */
    public static function documentoEnviado(Documento $documento): void
    {
        $proyecto = $documento->proyecto;
        if (!$proyecto) return;

        $cliente = $proyecto->cliente;
        if (!$cliente) return;

        Notificacion::paraCliente(
            $cliente,
            'documento_enviado',
            'Nuevo documento para revisar',
            route('portal.proyectos.show', $proyecto->id),
            $documento,
            "Se ha compartido el documento \"{$documento->nombre}\" en el proyecto {$proyecto->nombre}."
        );

        if ($cliente->email) {
            Mail::to($cliente->email)->queue(new DocumentoEnviado($documento));
        }
    }

    /**
     * Cliente aprueba documento → notificar al admin que lo subió
     */
    public static function documentoAprobado(Documento $documento): void
    {
        $proyecto = $documento->proyecto->load('usuariosCompartidos');
        $admins   = self::adminsDelProyecto($proyecto);

        foreach ($admins as $admin) {
            Notificacion::paraAdmin(
                $admin,
                'documento_aprobado',
                'Documento aprobado por el cliente',
                route('admin.proyectos.show', $proyecto->id),
                $documento,
                "El cliente aprobó y selló el documento \"{$documento->nombre}\" del proyecto {$proyecto->nombre}."
            );
            if ($admin->email) {
                Mail::to($admin->email)->queue(new DocumentoAprobado($documento));
            }
        }
    }

    // ── Cotizaciones ──────────────────────────────────────────────

    public static function cotizacionAprobada(\App\Models\Cotizacion $cotizacion): void
    {
        $admin = $cotizacion->creadoPor;
        if (!$admin) return;

        Notificacion::paraAdmin(
            $admin,
            'cotizacion_aprobada',
            'Cotización aprobada',
            route('admin.cotizaciones.edit', $cotizacion->id),
            $cotizacion,
            "El cliente aprobó la cotización \"{$cotizacion->nombre}\"."
        );

        if ($admin->email) {
            Mail::to($admin->email)->queue(new CotizacionEstadoCambiado($cotizacion, 'aprobada'));
        }
    }

    public static function cotizacionRechazada(\App\Models\Cotizacion $cotizacion): void
    {
        $admin = $cotizacion->creadoPor;
        if (!$admin) return;

        Notificacion::paraAdmin(
            $admin,
            'cotizacion_rechazada',
            'Cotización rechazada',
            route('admin.cotizaciones.edit', $cotizacion->id),
            $cotizacion,
            "El cliente rechazó la cotización \"{$cotizacion->nombre}\"."
        );

        if ($admin->email) {
            Mail::to($admin->email)->queue(new CotizacionEstadoCambiado($cotizacion, 'rechazada'));
        }
    }

    // ── Publicaciones ─────────────────────────────────────────────

    public static function publicacionAprobada(Publicacion $publicacion): void
    {
        $creador = User::find($publicacion->created_by);
        if (!$creador) return;

        Notificacion::paraAdmin(
            $creador,
            'publicacion_aprobada',
            'Publicación aprobada',
            route('admin.publicaciones.index'),
            $publicacion,
            "El cliente aprobó la publicación \"{$publicacion->titulo}\"."
        );
    }

    public static function publicacionRechazada(Publicacion $publicacion): void
    {
        $creador = User::find($publicacion->created_by);
        if (!$creador) return;

        Notificacion::paraAdmin(
            $creador,
            'publicacion_rechazada',
            'Publicación rechazada',
            route('admin.publicaciones.index'),
            $publicacion,
            "El cliente rechazó la publicación \"{$publicacion->titulo}\"."
        );
    }
}

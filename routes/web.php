<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Portal;

// Root redirect
Route::get('/', fn() => redirect()->route('admin.login'));

// ===== MARCA PÚBLICA (sin auth) =====
Route::get('/marca/{token}', [\App\Http\Controllers\MarcaPublicaController::class, 'show'])->name('marca.publica');
Route::get('/marca/{token}/recurso/{recurso}/download', [\App\Http\Controllers\MarcaPublicaController::class, 'descargar'])->name('marca.publica.download');

// ===== ADMIN ROUTES =====
Route::prefix('admin')->name('admin.')->group(function () {
    // Auth (guest)
    Route::middleware('guest')->group(function () {
        Route::get('/login', [Admin\AuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [Admin\AuthController::class, 'login'])->name('login.submit');
    });
    Route::post('/logout', [Admin\AuthController::class, 'logout'])->name('logout');

    // Protected admin routes
    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

        // Clients
        Route::resource('clientes', Admin\ClienteController::class);

        // Projects
        Route::resource('proyectos', Admin\ProyectoController::class);

        // Brief Creativo
        Route::get('/proyectos/{proyecto}/brief/edit', [Admin\BriefCreativoController::class, 'edit'])->name('proyectos.brief.edit');
        Route::put('/proyectos/{proyecto}/brief', [Admin\BriefCreativoController::class, 'update'])->name('proyectos.brief.update');

        // Comentarios (admin)
        Route::post('/comentarios', [Admin\ComentarioController::class, 'store'])->name('comentarios.store');
        Route::delete('/comentarios/{comentario}', [Admin\ComentarioController::class, 'destroy'])->name('comentarios.destroy');

        // Documents
        Route::post('/documentos', [Admin\DocumentoController::class, 'store'])->name('documentos.store');
        Route::post('/documentos/{documento}/enviar', [Admin\DocumentoController::class, 'enviar'])->name('documentos.enviar');
        Route::post('/documentos/{documento}/sellar', [Admin\DocumentoController::class, 'sellar'])->name('documentos.sellar');
        Route::delete('/documentos/{documento}', [Admin\DocumentoController::class, 'destroy'])->name('documentos.destroy');
        Route::get('/documentos/{documento}/download', [Admin\DocumentoController::class, 'download'])->name('documentos.download');
        Route::get('/documentos/{documento}/view', [Admin\DocumentoController::class, 'view'])->name('documentos.view');
        Route::get('/archivos/{archivo}/view', [Admin\DocumentoController::class, 'viewArchivo'])->name('archivos.view');
        Route::get('/archivos/{archivo}/download', [Admin\DocumentoController::class, 'downloadArchivo'])->name('archivos.download');

        // Deliveries
        Route::post('/entregas', [Admin\EntregaController::class, 'store'])->name('entregas.store');
        Route::delete('/entregas/{entrega}', [Admin\EntregaController::class, 'destroy'])->name('entregas.destroy');

        // Payments
        Route::get('/pagos', [Admin\PagoController::class, 'index'])->name('pagos.index');
        Route::post('/pagos', [Admin\PagoController::class, 'store'])->name('pagos.store');
        Route::post('/pagos/{pago}/marcar-pagado', [Admin\PagoController::class, 'marcarPagado'])->name('pagos.marcar-pagado');
        Route::get('/pagos/{pago}/generar-qr', [Admin\PagoController::class, 'generarQR'])->name('pagos.generar-qr');

        // Activity log
        Route::get('/actividad', [Admin\ActividadController::class, 'index'])->name('actividad.index');

        // Meta Tokens (Facebook & Instagram)
        Route::get('/meta-tokens', [Admin\MetaTokenController::class, 'index'])->name('meta-tokens.index');
        Route::post('/meta-tokens', [Admin\MetaTokenController::class, 'store'])->name('meta-tokens.store');
        Route::get('/meta-tokens/{metaToken}', [Admin\MetaTokenController::class, 'show'])->name('meta-tokens.show');
        Route::put('/meta-tokens/{metaToken}', [Admin\MetaTokenController::class, 'update'])->name('meta-tokens.update');
        Route::delete('/meta-tokens/{metaToken}', [Admin\MetaTokenController::class, 'destroy'])->name('meta-tokens.destroy');
        Route::post('/meta-tokens/{metaToken}/verificar', [Admin\MetaTokenController::class, 'verificar'])->name('meta-tokens.verificar');
        Route::post('/meta-tokens/detectar-ig', [Admin\MetaTokenController::class, 'detectarIg'])->name('meta-tokens.detectar-ig');

        // Publicaciones (Social Media Calendar)
        Route::get('/publicaciones', [Admin\PublicacionController::class, 'index'])->name('publicaciones.index');
        Route::get('/publicaciones/eventos', [Admin\PublicacionController::class, 'eventos'])->name('publicaciones.eventos');
        Route::post('/publicaciones/analizar-imagen', [Admin\PublicacionController::class, 'analizarImagen'])->name('publicaciones.analizar');
        Route::post('/publicaciones', [Admin\PublicacionController::class, 'store'])->name('publicaciones.store');
        Route::get('/publicaciones/{publicacion}', [Admin\PublicacionController::class, 'show'])->name('publicaciones.show');
        Route::put('/publicaciones/{publicacion}', [Admin\PublicacionController::class, 'update'])->name('publicaciones.update');
        Route::post('/publicaciones/{publicacion}/publicar', [Admin\PublicacionController::class, 'publicar'])->name('publicaciones.publicar');
        Route::delete('/publicaciones/{publicacion}', [Admin\PublicacionController::class, 'destroy'])->name('publicaciones.destroy');

        // Cotizaciones
        Route::resource('cotizaciones', Admin\CotizacionController::class)->parameters(['cotizaciones' => 'cotizacion']);
        Route::post('/cotizaciones/{cotizacion}/items', [Admin\CotizacionController::class, 'storeItem'])->name('cotizaciones.items.store');
        Route::delete('/cotizaciones/{cotizacion}/items/{item}', [Admin\CotizacionController::class, 'destroyItem'])->name('cotizaciones.items.destroy');
        Route::get('/cotizaciones-clientes/{cliente}/proyectos', [Admin\CotizacionController::class, 'proyectosPorCliente'])->name('cotizaciones.proyectos-cliente');

        // Plantillas de Cotizaciones
        Route::resource('plantillas-cotizacion', Admin\PlantillaCotizacionController::class)
            ->parameters(['plantillas-cotizacion' => 'plantilla'])
            ->names([
                'index'   => 'plantillas-cotizacion.index',
                'create'  => 'plantillas-cotizacion.create',
                'store'   => 'plantillas-cotizacion.store',
                'show'    => 'plantillas-cotizacion.show',
                'edit'    => 'plantillas-cotizacion.edit',
                'update'  => 'plantillas-cotizacion.update',
                'destroy' => 'plantillas-cotizacion.destroy',
            ]);
        Route::get('/plantillas-cotizacion/{plantilla}/items', [Admin\PlantillaCotizacionController::class, 'items'])
            ->name('plantillas-cotizacion.items');

        // Notificaciones (admin)
        Route::get('/notificaciones', [Admin\NotificacionController::class, 'index'])->name('notificaciones.index');
        Route::get('/notificaciones/recientes', [Admin\NotificacionController::class, 'recent'])->name('notificaciones.recientes');
        Route::post('/notificaciones/{notificacion}/leer', [Admin\NotificacionController::class, 'markRead'])->name('notificaciones.leer');
        Route::post('/notificaciones/leer-todas', [Admin\NotificacionController::class, 'markAllRead'])->name('notificaciones.leer-todas');

        // Marcas / Branding
        Route::resource('marcas', Admin\MarcaController::class)->parameters(['marcas' => 'marca']);
        Route::post('/marcas/{marca}/toggle-acceso', [Admin\MarcaController::class, 'toggleAcceso'])->name('marcas.toggle-acceso');
        Route::post('/marcas/{marca}/recursos', [Admin\MarcaController::class, 'subirRecurso'])->name('marcas.recursos.store');
        Route::delete('/marcas/{marca}/recursos/{recurso}', [Admin\MarcaController::class, 'eliminarRecurso'])->name('marcas.recursos.destroy');
        Route::get('/marcas/{marca}/recursos/{recurso}/download', [Admin\MarcaController::class, 'descargarRecurso'])->name('marcas.recursos.download');
        Route::get('/marcas/{marca}/exportar-zip', [Admin\MarcaController::class, 'exportarZip'])->name('marcas.exportar-zip');

        // Mailing
        Route::resource('mailing', Admin\CampanaEmailController::class)->parameters(['mailing' => 'mailing']);
        Route::post('/mailing/{mailing}/enviar', [Admin\CampanaEmailController::class, 'enviar'])->name('mailing.enviar');
        Route::get('/mailing/{mailing}/preview', [Admin\CampanaEmailController::class, 'preview'])->name('mailing.preview');
        Route::post('/mailing/preview-live', [Admin\CampanaEmailController::class, 'previewLive'])->name('mailing.preview-live');

        // Perfil del usuario autenticado
        Route::get('/perfil', [Admin\ProfileController::class, 'show'])->name('perfil.show');
        Route::get('/perfil/editar', [Admin\ProfileController::class, 'edit'])->name('perfil.edit');
        Route::put('/perfil', [Admin\ProfileController::class, 'update'])->name('perfil.update');
        Route::put('/perfil/password', [Admin\ProfileController::class, 'updatePassword'])->name('perfil.password');
        Route::delete('/perfil/foto', [Admin\ProfileController::class, 'destroyFoto'])->name('perfil.foto.destroy');

        // Gestión de usuarios (solo superadmin)
        Route::middleware('superadmin')->group(function () {
            Route::resource('usuarios', Admin\UserController::class)->parameters(['usuarios' => 'usuario']);
        });

        // Compartir proyectos entre usuarios
        Route::post('/proyectos/{proyecto}/compartir', [Admin\ProyectoController::class, 'compartirUsuario'])->name('proyectos.compartir-usuario');
        Route::delete('/proyectos/{proyecto}/usuarios/{usuario}', [Admin\ProyectoController::class, 'quitarUsuario'])->name('proyectos.quitar-usuario');

        // Tiempo
        Route::get('/tiempo', [Admin\TiempoController::class, 'index'])->name('tiempo.index');
        Route::post('/tiempo', [Admin\TiempoController::class, 'store'])->name('tiempo.store');
        Route::delete('/tiempo/{registro}', [Admin\TiempoController::class, 'destroy'])->name('tiempo.destroy');
        Route::post('/tiempo/timer/iniciar', [Admin\TiempoController::class, 'iniciarTimer'])->name('tiempo.timer.iniciar');
        Route::post('/tiempo/timer/detener', [Admin\TiempoController::class, 'detenerTimer'])->name('tiempo.timer.detener');

        // Rentabilidad
        Route::get('/rentabilidad', [Admin\RentabilidadController::class, 'index'])->name('rentabilidad.index');

        // Facturas & Recibos
        Route::resource('facturas', Admin\FacturaController::class)->parameters(['facturas' => 'factura']);
        Route::post('/facturas/{factura}/enviar',       [Admin\FacturaController::class, 'enviar'])->name('facturas.enviar');
        Route::post('/facturas/{factura}/marcar-pagada',[Admin\FacturaController::class, 'marcarPagada'])->name('facturas.marcar-pagada');
        Route::post('/facturas/{factura}/cancelar',     [Admin\FacturaController::class, 'cancelar'])->name('facturas.cancelar');
        Route::post('/facturas/{factura}/duplicar',     [Admin\FacturaController::class, 'duplicar'])->name('facturas.duplicar');
        Route::get('/facturas/{factura}/pdf',           [Admin\FacturaController::class, 'pdf'])->name('facturas.pdf');
    });
});

// ===== CLIENT PORTAL ROUTES =====
Route::prefix('portal')->name('portal.')->group(function () {
    // Auth (guest)
    Route::middleware('guest')->group(function () {
        Route::get('/login', [Portal\AuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [Portal\AuthController::class, 'login'])->name('login.submit');
    });
    Route::post('/logout', [Portal\AuthController::class, 'logout'])->name('logout');

    // Protected portal routes
    Route::middleware(['auth', 'client.portal'])->group(function () {
        Route::get('/dashboard', [Portal\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/manual', [Portal\DashboardController::class, 'manual'])->name('manual');

        Route::get('/proyectos/{proyecto}', [Portal\ProyectoController::class, 'show'])->name('proyectos.show');
        Route::post('/entregas/{entrega}/aprobar', [Portal\ProyectoController::class, 'aprobarEntrega'])->name('entregas.aprobar');
        Route::post('/entregas/{entrega}/rechazar', [Portal\ProyectoController::class, 'rechazarEntrega'])->name('entregas.rechazar');
        Route::post('/documentos/{documento}/aprobar', [Portal\ProyectoController::class, 'aprobarDocumento'])->name('documentos.aprobar');

        Route::get('/documentos/{documento}/view', [Portal\DocumentoController::class, 'view'])->name('documentos.view');
        Route::get('/documentos/{documento}/download', [Portal\DocumentoController::class, 'download'])->name('documentos.download');
        Route::get('/archivos/{archivo}/view', [Portal\DocumentoController::class, 'viewArchivo'])->name('archivos.view');

        Route::get('/pagos', [Portal\PagoController::class, 'index'])->name('pagos.index');

        // Comentarios (portal)
        Route::post('/comentarios', [Portal\ComentarioController::class, 'store'])->name('comentarios.store');

        // Notificaciones (portal)
        Route::get('/notificaciones', [Portal\NotificacionController::class, 'index'])->name('notificaciones.index');
        Route::get('/notificaciones/recientes', [Portal\NotificacionController::class, 'recent'])->name('notificaciones.recientes');
        Route::post('/notificaciones/{notificacion}/leer', [Portal\NotificacionController::class, 'markRead'])->name('notificaciones.leer');
        Route::post('/notificaciones/leer-todas', [Portal\NotificacionController::class, 'markAllRead'])->name('notificaciones.leer-todas');

        // Publicaciones (Social Media Calendar)
        Route::get('/publicaciones', [Portal\PublicacionController::class, 'index'])->name('publicaciones.index');
        Route::get('/publicaciones/eventos', [Portal\PublicacionController::class, 'eventos'])->name('publicaciones.eventos');
        Route::post('/publicaciones/{publicacion}/aprobar', [Portal\PublicacionController::class, 'aprobar'])->name('publicaciones.aprobar');
        Route::post('/publicaciones/{publicacion}/rechazar', [Portal\PublicacionController::class, 'rechazar'])->name('publicaciones.rechazar');
    });
});

// ===== COTIZACIONES PÚBLICAS (sin auth) =====
Route::prefix('cotizacion')->name('cotizacion.')->group(function () {
    Route::get('/{token}', [\App\Http\Controllers\Public\CotizacionPublicaController::class, 'show'])->name('publica');
    Route::post('/{token}/aprobar', [\App\Http\Controllers\Public\CotizacionPublicaController::class, 'aprobar'])->name('aprobar');
    Route::post('/{token}/rechazar', [\App\Http\Controllers\Public\CotizacionPublicaController::class, 'rechazar'])->name('rechazar');
});

// Fallback
Route::fallback(fn() => redirect()->route('admin.login'));

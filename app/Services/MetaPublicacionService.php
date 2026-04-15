<?php

namespace App\Services;

use App\Models\MetaToken;
use App\Models\Publicacion;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MetaPublicacionService
{
    private const GRAPH_URL = 'https://graph.facebook.com/v21.0';

    /**
     * Publica una publicación aprobada en Meta (Facebook o Instagram).
     * Devuelve ['ok' => true/false, 'mensaje' => '...', 'post_id' => '...']
     */
    public function publicar(Publicacion $publicacion): array
    {
        // Buscar token activo para este cliente y red social
        $plataforma = in_array($publicacion->red_social, ['instagram']) ? 'instagram' : 'facebook';
        // Twitter/LinkedIn/TikTok no son Meta → no aplica
        if (!in_array($publicacion->red_social, ['instagram', 'facebook'])) {
            return ['ok' => false, 'mensaje' => "Red social {$publicacion->red_social} no es compatible con la publicación automática de Meta."];
        }

        $token = MetaToken::where('cliente_id', $publicacion->cliente_id)
            ->where('plataforma', $plataforma)
            ->where('activo', true)
            ->first();

        if (!$token) {
            return ['ok' => false, 'mensaje' => "No hay token activo de {$plataforma} para este cliente."];
        }

        if ($plataforma === 'facebook') {
            return $this->publicarFacebook($publicacion, $token);
        }

        return $this->publicarInstagram($publicacion, $token);
    }

    // -------------------------------------------------------
    // Facebook
    // -------------------------------------------------------
    private function publicarFacebook(Publicacion $publicacion, MetaToken $token): array
    {
        $caption = $publicacion->descripcion;
        $archivoUrl = $this->getPublicUrl($publicacion->archivo_path);
        $esVideo = $publicacion->archivo_path && $this->esVideo($publicacion->archivo_path);

        try {
            if ($archivoUrl && !$esVideo) {
                // Post con imagen
                $response = Http::post(self::GRAPH_URL . "/{$token->page_id}/photos", [
                    'url'          => $archivoUrl,
                    'message'      => $caption,
                    'access_token' => $token->access_token,
                ]);
            } elseif ($archivoUrl && $esVideo) {
                // Post con video (Facebook Reels/Video)
                $response = Http::post(self::GRAPH_URL . "/{$token->page_id}/videos", [
                    'file_url'     => $archivoUrl,
                    'description'  => $caption,
                    'access_token' => $token->access_token,
                ]);
            } else {
                // Post de texto
                $response = Http::post(self::GRAPH_URL . "/{$token->page_id}/feed", [
                    'message'      => $caption,
                    'access_token' => $token->access_token,
                ]);
            }

            if ($response->successful() && $response->json('id')) {
                return ['ok' => true, 'mensaje' => 'Publicado en Facebook.', 'post_id' => $response->json('id')];
            }

            $error = $response->json('error.message') ?? $response->body();
            return ['ok' => false, 'mensaje' => "Error Facebook: {$error}"];

        } catch (\Throwable $e) {
            Log::error('MetaPublicacionService::publicarFacebook', ['error' => $e->getMessage(), 'pub_id' => $publicacion->id]);
            return ['ok' => false, 'mensaje' => 'Excepción al publicar en Facebook: ' . $e->getMessage()];
        }
    }

    // -------------------------------------------------------
    // Instagram
    // -------------------------------------------------------
    private function publicarInstagram(Publicacion $publicacion, MetaToken $token): array
    {
        if (!$token->ig_account_id) {
            return ['ok' => false, 'mensaje' => 'El token de Instagram no tiene configurado el ID de cuenta. Configúralo en Tokens de Meta.'];
        }

        $caption  = $publicacion->descripcion;
        $archivoUrl = $this->getPublicUrl($publicacion->archivo_path);
        $esVideo  = $publicacion->archivo_path && $this->esVideo($publicacion->archivo_path);

        try {
            // Paso 1: Crear contenedor de media
            $params = [
                'caption'      => $caption,
                'access_token' => $token->access_token,
            ];

            if ($archivoUrl && $esVideo) {
                $params['media_type'] = 'REELS';
                $params['video_url']  = $archivoUrl;
            } elseif ($archivoUrl) {
                $params['image_url'] = $archivoUrl;
            } else {
                // Instagram requiere media — si no hay, fallback a texto (no soportado directamente)
                return ['ok' => false, 'mensaje' => 'Instagram requiere una imagen o video para publicar.'];
            }

            $containerResp = Http::post(self::GRAPH_URL . "/{$token->ig_account_id}/media", $params);

            if (!$containerResp->successful() || !$containerResp->json('id')) {
                $error = $containerResp->json('error.message') ?? $containerResp->body();
                return ['ok' => false, 'mensaje' => "Error creando contenedor Instagram: {$error}"];
            }

            $creationId = $containerResp->json('id');

            // Para videos, esperar a que el contenedor esté listo
            if ($esVideo) {
                $intentos = 0;
                do {
                    sleep(5);
                    $statusResp = Http::get(self::GRAPH_URL . "/{$creationId}", [
                        'fields'       => 'status_code',
                        'access_token' => $token->access_token,
                    ]);
                    $status = $statusResp->json('status_code');
                    $intentos++;
                } while ($status !== 'FINISHED' && $status !== 'ERROR' && $intentos < 12);

                if ($status === 'ERROR' || $intentos >= 12) {
                    return ['ok' => false, 'mensaje' => 'El video no terminó de procesarse en Instagram.'];
                }
            }

            // Paso 2: Publicar
            $publishResp = Http::post(self::GRAPH_URL . "/{$token->ig_account_id}/media_publish", [
                'creation_id'  => $creationId,
                'access_token' => $token->access_token,
            ]);

            if ($publishResp->successful() && $publishResp->json('id')) {
                return ['ok' => true, 'mensaje' => 'Publicado en Instagram.', 'post_id' => $publishResp->json('id')];
            }

            $error = $publishResp->json('error.message') ?? $publishResp->body();
            return ['ok' => false, 'mensaje' => "Error publicando en Instagram: {$error}"];

        } catch (\Throwable $e) {
            Log::error('MetaPublicacionService::publicarInstagram', ['error' => $e->getMessage(), 'pub_id' => $publicacion->id]);
            return ['ok' => false, 'mensaje' => 'Excepción al publicar en Instagram: ' . $e->getMessage()];
        }
    }

    // -------------------------------------------------------
    // Verificar token
    // -------------------------------------------------------
    public function verificarToken(MetaToken $token): array
    {
        try {
            $resp = Http::get(self::GRAPH_URL . '/me', [
                'access_token' => $token->access_token,
                'fields'       => 'id,name',
            ]);

            if ($resp->successful() && $resp->json('id')) {
                return ['ok' => true, 'nombre' => $resp->json('name'), 'id' => $resp->json('id')];
            }

            $error = $resp->json('error.message') ?? 'Token inválido';
            return ['ok' => false, 'mensaje' => $error];

        } catch (\Throwable $e) {
            return ['ok' => false, 'mensaje' => $e->getMessage()];
        }
    }

    /**
     * Detecta el Instagram Business Account ID a partir del Page ID y el token.
     */
    public function detectarIgAccountId(string $pageId, string $accessToken): array
    {
        try {
            $resp = Http::get(self::GRAPH_URL . "/{$pageId}", [
                'fields'       => 'instagram_business_account',
                'access_token' => $accessToken,
            ]);

            $igId = $resp->json('instagram_business_account.id');
            if ($igId) {
                return ['ok' => true, 'ig_account_id' => $igId];
            }

            return ['ok' => false, 'mensaje' => 'No se encontró una cuenta de Instagram Business vinculada a esta página.'];

        } catch (\Throwable $e) {
            return ['ok' => false, 'mensaje' => $e->getMessage()];
        }
    }

    // -------------------------------------------------------
    // Helpers
    // -------------------------------------------------------
    private function getPublicUrl(?string $path): ?string
    {
        if (!$path) return null;

        $appUrl = rtrim(config('app.url'), '/');

        // En localhost Meta no puede acceder → devolver null para no bloquear
        if (str_contains($appUrl, 'localhost') || str_contains($appUrl, '127.0.0.1')) {
            Log::warning("MetaPublicacionService: APP_URL es localhost, se publicará sin media.", ['path' => $path]);
            return null;
        }

        return $appUrl . Storage::url($path);
    }

    private function esVideo(string $path): bool
    {
        return (bool) preg_match('/\.(mp4|mov|webm|avi|mkv)$/i', $path);
    }
}

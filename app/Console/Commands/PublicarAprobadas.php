<?php

namespace App\Console\Commands;

use App\Models\Publicacion;
use App\Services\MetaPublicacionService;
use Illuminate\Console\Command;

class PublicarAprobadas extends Command
{
    protected $signature   = 'publicaciones:publicar {--dry-run : Simula sin publicar realmente}';
    protected $description = 'Publica en Meta las publicaciones aprobadas cuya fecha programada ya pasó';

    public function handle(MetaPublicacionService $meta): int
    {
        $dryRun = $this->option('dry-run');

        $pendientes = Publicacion::with(['cliente'])
            ->where('estado', 'aprobado')
            ->where('fecha_programada', '<=', now())
            ->whereIn('red_social', ['facebook', 'instagram'])
            ->orderBy('fecha_programada')
            ->get();

        if ($pendientes->isEmpty()) {
            $this->info('No hay publicaciones aprobadas pendientes de publicar.');
            return Command::SUCCESS;
        }

        $this->info("Encontradas {$pendientes->count()} publicaciones para procesar.");
        $this->newLine();

        $ok    = 0;
        $error = 0;

        foreach ($pendientes as $pub) {
            $label = "[#{$pub->id}] {$pub->titulo} ({$pub->red_social}) — {$pub->cliente->nombre_empresa}";
            $this->line("  Procesando: {$label}");

            if ($dryRun) {
                $this->warn("  → [DRY-RUN] Se publicaría pero no se envía.");
                $ok++;
                continue;
            }

            $resultado = $meta->publicar($pub);

            if ($resultado['ok']) {
                $pub->update([
                    'estado'            => 'publicado',
                    'error_publicacion' => null,
                ]);
                $this->info("  ✓ Publicado: {$resultado['mensaje']}");
                $ok++;
            } else {
                $pub->update([
                    'estado'            => 'error',
                    'error_publicacion' => $resultado['mensaje'],
                ]);
                $this->error("  ✗ Error: {$resultado['mensaje']}");
                $error++;
            }

            $this->newLine();
        }

        $this->table(
            ['Estado', 'Cantidad'],
            [['✓ Publicados', $ok], ['✗ Con error', $error]]
        );

        return Command::SUCCESS;
    }
}

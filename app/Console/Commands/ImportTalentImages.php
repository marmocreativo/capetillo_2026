<?php

namespace App\Console\Commands;

use App\Models\Talent;
use App\Services\ImageUploadService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ImportTalentImages extends Command
{
    protected $signature = 'talents:import-images {--overwrite : Sobrescribir imágenes ya existentes}';

    protected $description = 'Descarga las imágenes de los talentos desde el sitio WordPress original y las asigna como cover_image';

    /**
     * Mapeo de nombre de categoría en tu BD => slug real de la categoría en el WordPress viejo.
     */
    protected array $categoryUrlMap = [
        'Comediantes' => 'comediantes',
        'Standuperos' => 'standuperos',
        'Youtuberos' => 'youtuberos',
        'Conductores' => 'conductores',
        'Artistas Musicales y Atractivo Visual' => 'artistas-musicales-y-atractivo-visual',
        'Grupos y Bandas Musicales' => 'grupos-musicales',
        'Banda' => 'banda',
        'Trios románticos' => 'trios-romanticos',
        'Mariachis' => 'comediantes-3',
        'Música Pop, Romántica, Rock y Urbana' => 'musica-pop-romantica-rock-y-urbana',
        'Conferencistas' => 'conferencistas',
        'Actores y Actrices' => 'actores-y-actrices',
        'Regional Mexicano y Ranchero' => 'regional-mexicano-y-ranchero',
    ];

    protected string $baseUrl = 'https://capetilloproducciones.mx';

    public function handle(ImageUploadService $imageService): int
    {
        $overwrite = $this->option('overwrite');
        $found = 0;
        $skipped = 0;
        $notMatched = 0;
        $failed = 0;

        foreach ($this->categoryUrlMap as $categoryName => $urlSlug) {
            $this->info("Procesando categoría: {$categoryName} ({$urlSlug})");

            $response = Http::timeout(30)->get("{$this->baseUrl}/{$urlSlug}/");

            if ($response->failed()) {
                $this->warn("  No se pudo descargar la página: {$urlSlug}");
                continue;
            }

            $html = $response->body();

            preg_match_all('/<a\s+href="([^"]+)">\s*<img[^>]*\bsrc="([^"]+)"/s', $html, $matches, PREG_SET_ORDER);

            $this->line('  Enlaces encontrados: ' . count($matches));

            foreach ($matches as $match) {
                $href = $match[1];
                $imgSrc = $match[2];

                $path = trim(parse_url($href, PHP_URL_PATH) ?? '', '/');
                $segments = explode('/', $path);
                $slug = end($segments);

                if (empty($slug) || $slug === 'contrataciones') {
                    // Enlace genérico sin página propia, no se puede matchear.
                    $notMatched++;
                    continue;
                }

                $talent = Talent::where('slug', $slug)->first();

                if (! $talent) {
                    $this->line("  ⚠ No existe talento con slug: {$slug}");
                    $notMatched++;
                    continue;
                }

                if ($talent->cover_image && ! $overwrite) {
                    $skipped++;
                    continue;
                }

                try {
                    $storedPath = $imageService->storeFromUrl($imgSrc, 'talents');

                    if (! $storedPath) {
                        $this->line("  ✗ Falló la descarga de imagen para: {$talent->name}");
                        $failed++;
                        continue;
                    }

                    $talent->update(['cover_image' => $storedPath]);
                    $this->line("  ✓ {$talent->name}");
                    $found++;
                } catch (\Throwable $e) {
                    $this->line("  ✗ Error con {$talent->name}: " . $e->getMessage());
                    $failed++;
                }
            }
        }

        $this->newLine();
        $this->info("Resumen:");
        $this->line("  Imágenes asignadas: {$found}");
        $this->line("  Omitidas (ya tenían imagen): {$skipped}");
        $this->line("  Sin match en BD: {$notMatched}");
        $this->line("  Fallidas: {$failed}");

        return self::SUCCESS;
    }
}
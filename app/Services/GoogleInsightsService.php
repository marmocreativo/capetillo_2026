<?php

namespace App\Services;

use App\Models\Talent;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Visitas (GA4 Data API) y búsquedas de Google (Search Console API) de la página pública de un talento.
 * Usa una cuenta de servicio y las APIs REST directamente (sin google/apiclient).
 */
class GoogleInsightsService
{
    private const SCOPES = [
        'https://www.googleapis.com/auth/analytics.readonly',
        'https://www.googleapis.com/auth/webmasters.readonly',
    ];

    public function credentialsConfigured(): bool
    {
        return is_file($this->credentialsPath());
    }

    public function analyticsConfigured(): bool
    {
        return $this->credentialsConfigured() && filled(config('services.google.ga4_property_id'));
    }

    public function searchConsoleConfigured(): bool
    {
        return $this->credentialsConfigured() && filled(config('services.google.search_console_site'));
    }

    /** Resultado completo para el popup; cada sección falla de forma independiente. */
    public function forTalent(Talent $talent): array
    {
        $result = [
            'analytics' => $this->section($this->analyticsConfigured(), fn () => $this->analytics($talent->slug)),
            'searchConsole' => $this->section($this->searchConsoleConfigured(), fn () => $this->searchConsole($talent->slug)),
        ];

        return $result;
    }

    /** Aísla cada API: si una falla, la otra sigue. Las respuestas correctas se cachean 6 h (los errores no). */
    private function section(bool $configured, callable $fetch): array
    {
        if (! $configured) {
            return ['status' => 'not_configured'];
        }

        try {
            return ['status' => 'ok', 'data' => $fetch()];
        } catch (\Throwable $e) {
            report($e);

            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /** Vistas y usuarios de los últimos 30 días vs. los 30 anteriores. */
    public function analytics(string $slug): array
    {
        return Cache::remember("google-insights:ga:{$slug}", now()->addHours(6), function () use ($slug) {
            $property = config('services.google.ga4_property_id');

            $response = Http::withToken($this->accessToken())
                ->post("https://analyticsdata.googleapis.com/v1beta/properties/{$property}:runReport", [
                    'dateRanges' => [
                        ['startDate' => '30daysAgo', 'endDate' => 'today', 'name' => 'current'],
                        ['startDate' => '60daysAgo', 'endDate' => '31daysAgo', 'name' => 'previous'],
                    ],
                    'metrics' => [['name' => 'screenPageViews'], ['name' => 'activeUsers']],
                    'dimensionFilter' => ['filter' => [
                        'fieldName' => 'pagePath',
                        'stringFilter' => ['matchType' => 'FULL_REGEXP', 'value' => $this->pathRegex($slug)],
                    ]],
                ]);

            $this->ensureOk($response, 'Google Analytics');

            $out = [
                'current' => ['views' => 0, 'users' => 0],
                'previous' => ['views' => 0, 'users' => 0],
            ];

            foreach ($response->json('rows', []) as $row) {
                $range = $row['dimensionValues'][0]['value'] ?? null;
                if (isset($out[$range])) {
                    $out[$range] = [
                        'views' => (int) ($row['metricValues'][0]['value'] ?? 0),
                        'users' => (int) ($row['metricValues'][1]['value'] ?? 0),
                    ];
                }
            }

            return $out;
        });
    }

    /** Búsquedas de Google que mostraron la página (últimos 28 días; Search Console tarda ~3 días). */
    public function searchConsole(string $slug): array
    {
        return Cache::remember("google-insights:gsc:{$slug}", now()->addHours(6), function () use ($slug) {
            $site = rawurlencode(config('services.google.search_console_site'));
            $url = "https://www.googleapis.com/webmasters/v3/sites/{$site}/searchAnalytics/query";

            $base = [
                'startDate' => now()->subDays(30)->toDateString(),
                'endDate' => now()->subDays(3)->toDateString(),
                'dimensionFilterGroups' => [[
                    'filters' => [[
                        'dimension' => 'page',
                        'operator' => 'includingRegex',
                        'expression' => $this->pathRegex($slug),
                    ]],
                ]],
            ];

            $token = $this->accessToken();

            $queries = Http::withToken($token)->post($url, $base + ['dimensions' => ['query'], 'rowLimit' => 10]);
            $this->ensureOk($queries, 'Search Console');

            $totals = Http::withToken($token)->post($url, $base);
            $this->ensureOk($totals, 'Search Console');

            $total = $totals->json('rows.0', []);

            return [
                'impressions' => (int) ($total['impressions'] ?? 0),
                'clicks' => (int) ($total['clicks'] ?? 0),
                'position' => ! empty($total['impressions']) && isset($total['position']) ? round($total['position'], 1) : null,
                'period' => $base['startDate'] . ' → ' . $base['endDate'],
                'queries' => collect($queries->json('rows', []))->map(fn ($r) => [
                    'query' => $r['keys'][0] ?? '',
                    'impressions' => (int) $r['impressions'],
                    'clicks' => (int) $r['clicks'],
                    'position' => round($r['position'], 1),
                ])->all(),
            ];
        });
    }

    /** Las páginas públicas de talento tienen la forma /{categoria}/{slug}. */
    private function pathRegex(string $slug): string
    {
        return '.*/[^/]+/' . preg_quote($slug, '/') . '/?';
    }

    private function ensureOk($response, string $api): void
    {
        if ($response->failed()) {
            $msg = $response->json('error.message') ?? $response->reason();
            throw new RuntimeException("{$api}: {$msg}");
        }
    }

    /** Token OAuth2 de la cuenta de servicio (JWT bearer), cacheado casi una hora. */
    private function accessToken(): string
    {
        return Cache::remember('google-insights:token', now()->addMinutes(50), function () {
            $creds = json_decode(file_get_contents($this->credentialsPath()), true);

            if (! isset($creds['client_email'], $creds['private_key'])) {
                throw new RuntimeException('El archivo de credenciales de Google no es válido.');
            }

            $b64 = fn (string $d) => rtrim(strtr(base64_encode($d), '+/', '-_'), '=');
            $now = time();
            $unsigned = $b64(json_encode(['alg' => 'RS256', 'typ' => 'JWT'])) . '.' . $b64(json_encode([
                'iss' => $creds['client_email'],
                'scope' => implode(' ', self::SCOPES),
                'aud' => 'https://oauth2.googleapis.com/token',
                'iat' => $now,
                'exp' => $now + 3600,
            ]));

            if (! openssl_sign($unsigned, $signature, $creds['private_key'], OPENSSL_ALGO_SHA256)) {
                throw new RuntimeException('No se pudo firmar la solicitud a Google.');
            }

            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $unsigned . '.' . $b64($signature),
            ]);

            if ($response->failed() || ! $response->json('access_token')) {
                throw new RuntimeException('Google rechazó las credenciales: ' . ($response->json('error_description') ?? $response->reason()));
            }

            return $response->json('access_token');
        });
    }

    private function credentialsPath(): string
    {
        $path = (string) config('services.google.credentials');

        return preg_match('#^([A-Za-z]:[\\\\/]|/)#', $path) ? $path : storage_path('app/' . ltrim($path, '/\\'));
    }
}

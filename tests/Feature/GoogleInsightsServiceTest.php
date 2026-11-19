<?php

use App\Models\Talent;
use App\Services\GoogleInsightsService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    Cache::flush();

    // En Windows OpenSSL necesita un openssl.cnf para generar claves.
    $opts = ['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA];
    $cnf = glob(dirname(PHP_BINARY) . '/extras/ssl/openssl.cnf')[0] ?? null;
    if ($cnf) {
        $opts['config'] = $cnf;
    }

    $key = openssl_pkey_new($opts);
    if (! $key) {
        $this->markTestSkipped('OpenSSL no puede generar claves en este entorno.');
    }
    openssl_pkey_export($key, $pem, null, $cnf ? ['config' => $cnf] : []);

    $this->credentials = sys_get_temp_dir() . '/google-test-credentials-' . uniqid() . '.json';
    file_put_contents($this->credentials, json_encode([
        'client_email' => 'test@proyecto.iam.gserviceaccount.com',
        'private_key' => $pem,
    ]));

    config([
        'services.google.credentials' => $this->credentials,
        'services.google.ga4_property_id' => '123456',
        'services.google.search_console_site' => 'sc-domain:ejemplo.mx',
    ]);
});

afterEach(fn () => @unlink($this->credentials));

it('reporta no configurado cuando faltan credenciales', function () {
    config(['services.google.credentials' => 'no-existe.json']);

    $talent = new Talent(['slug' => 'juan-perez']);
    $result = app(GoogleInsightsService::class)->forTalent($talent);

    expect($result['analytics']['status'])->toBe('not_configured')
        ->and($result['searchConsole']['status'])->toBe('not_configured');
});

it('obtiene visitas y búsquedas de Google', function () {
    Http::fake([
        'oauth2.googleapis.com/*' => Http::response(['access_token' => 'tok123']),
        'analyticsdata.googleapis.com/*' => Http::response(['rows' => [
            ['dimensionValues' => [['value' => 'current']], 'metricValues' => [['value' => '120'], ['value' => '80']]],
            ['dimensionValues' => [['value' => 'previous']], 'metricValues' => [['value' => '100'], ['value' => '70']]],
        ]]),
        'www.googleapis.com/webmasters/*' => Http::sequence()
            ->push(['rows' => [['keys' => ['juan perez contratacion'], 'impressions' => 50, 'clicks' => 5, 'position' => 3.456]]])
            ->push(['rows' => [['impressions' => 200, 'clicks' => 12, 'position' => 4.21]]]),
    ]);

    $result = app(GoogleInsightsService::class)->forTalent(new Talent(['slug' => 'juan-perez']));

    expect($result['analytics']['status'])->toBe('ok')
        ->and($result['analytics']['data']['current'])->toBe(['views' => 120, 'users' => 80])
        ->and($result['analytics']['data']['previous'])->toBe(['views' => 100, 'users' => 70])
        ->and($result['searchConsole']['data']['impressions'])->toBe(200)
        ->and($result['searchConsole']['data']['queries'][0])->toBe([
            'query' => 'juan perez contratacion', 'impressions' => 50, 'clicks' => 5, 'position' => 3.5,
        ]);

    // El token se firma como JWT y la propiedad/filtro apuntan al slug correcto.
    Http::assertSent(fn ($r) => str_contains($r->url(), 'oauth2.googleapis.com')
        && str_contains($r['assertion'], '.') && substr_count($r['assertion'], '.') === 2);
    Http::assertSent(fn ($r) => str_contains($r->url(), 'properties/123456:runReport')
        && str_contains($r['dimensionFilter']['filter']['stringFilter']['value'], 'juan\-perez'));
    Http::assertSent(fn ($r) => str_contains($r->url(), 'sites/sc-domain%3Aejemplo.mx/searchAnalytics/query'));
});

it('aísla los errores de cada API y no los cachea', function () {
    Http::fake([
        'oauth2.googleapis.com/*' => Http::response(['access_token' => 'tok123']),
        'analyticsdata.googleapis.com/*' => Http::response(['error' => ['message' => 'Sin permiso']], 403),
        'www.googleapis.com/webmasters/*' => Http::response(['rows' => []]),
    ]);

    $result = app(GoogleInsightsService::class)->forTalent(new Talent(['slug' => 'juan-perez']));

    expect($result['analytics']['status'])->toBe('error')
        ->and($result['analytics']['message'])->toContain('Sin permiso')
        ->and($result['searchConsole']['status'])->toBe('ok');
    expect(Cache::has('google-insights:ga:juan-perez'))->toBeFalse();
});

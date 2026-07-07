<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class GeminiContentService
{
    public function generateTalentContent(string $talentName, array $categoryNames): array
{
    $categories = implode(', ', $categoryNames);

    $prompt = <<<PROMPT
Eres un redactor experto en SEO y marketing de espectáculos para "Capetillo Producciones",
una agencia mexicana de contratación de talento artístico.

Investiga y redacta contenido para el siguiente talento:

Nombre: {$talentName}
Categoría(s): {$categories}

Investiga su trayectoria real, logros, estilo y por qué es popular (usa la búsqueda si es necesario).

Genera 4 bloques de texto EXACTAMENTE con este formato, usando los delimitadores tal cual
(no agregues nada antes del primer delimitador ni después del último):

===CONTENT===
(Biografía promocional en HTML, usando etiquetas <p> por párrafo, 4 a 5 párrafos.
Tono cálido, profesional y persuasivo, orientado a que la contraten para un evento.
Optimizado de forma natural para la frase clave "Contrataciones {$talentName}".
NO incluyas formularios, botones ni datos de contacto.)

===META_TITLE===
(Título SEO para la página, máximo 60 caracteres, debe incluir "Contrataciones {$talentName}")

===META_DESCRIPTION===
(Meta descripción SEO, máximo 155 caracteres, persuasiva, invita a contratarlo)

===META_KEYWORDS===
(5 a 8 palabras clave separadas por comas, relacionadas con el talento y "contrataciones {$talentName}")
PROMPT;

    $response = Http::withHeaders([
        'x-goog-api-key' => config('services.gemini.key'),
        'Content-Type' => 'application/json',
    ])->timeout(60)->post(
        'https://generativelanguage.googleapis.com/v1beta/models/' . config('services.gemini.model') . ':generateContent',
        [
            'contents' => [
                ['parts' => [['text' => $prompt]]],
            ],
            'tools' => [
                ['google_search' => new \stdClass()],
            ],
        ]
    );

    if ($response->failed()) {
        throw new RuntimeException('Error al generar contenido con Gemini: ' . $response->body());
    }

    $text = data_get($response->json(), 'candidates.0.content.parts.0.text');

    if (empty($text)) {
        throw new RuntimeException('Gemini no devolvió contenido.');
    }

    return $this->parseSections($text);
}

protected function parseSections(string $text): array
{
    $pattern = '/===(CONTENT|META_TITLE|META_DESCRIPTION|META_KEYWORDS)===\s*(.*?)(?=(===[A-Z_]+===|$))/s';
    preg_match_all($pattern, $text, $matches, PREG_SET_ORDER);

    $sections = [
        'content' => '',
        'meta_title' => '',
        'meta_description' => '',
        'meta_keywords' => '',
    ];

    foreach ($matches as $match) {
        $key = strtolower($match[1]);
        $sections[$key] = trim($match[2]);
    }

    $sections['content'] = trim(str_replace(['```html', '```'], '', $sections['content']));

    return $sections;
}

public function generateExtraFields(string $talentName, array $categoryNames): array
{
    $categories = implode(', ', $categoryNames);

    $prompt = <<<PROMPT
Eres un investigador que busca información verificable sobre artistas para "Capetillo Producciones".

Investiga al siguiente talento usando la búsqueda de Google. Haz búsquedas específicas como
"{$talentName} spotify" y "{$talentName} youtube oficial" para encontrar sus perfiles reales.

Nombre: {$talentName}
Categoría(s): {$categories}

Genera 2 bloques EXACTAMENTE con este formato (no agregues nada antes del primero ni después del último):

===SUMMARY===
(Una sola oración de máximo 160 caracteres que resuma quién es, para usarse como bajada bajo el título)

===HIGHLIGHTS===
(3 a 5 bullets de logros o datos destacados reales, uno por línea, cada línea empieza con "- ".
Si no encuentras logros verificables, escribe solo "- Amplia trayectoria artística")
PROMPT;

    $response = Http::withHeaders([
        'x-goog-api-key' => config('services.gemini.key'),
        'Content-Type' => 'application/json',
    ])->timeout(90)->post(
        'https://generativelanguage.googleapis.com/v1beta/models/' . config('services.gemini.pro_model') . ':generateContent',
        [
            'contents' => [
                ['parts' => [['text' => $prompt]]],
            ],
            'tools' => [
                ['google_search' => new \stdClass()],
            ],
        ]
    );

    if ($response->failed()) {
        throw new RuntimeException('Error al generar campos extra con Gemini: ' . $response->body());
    }

    $json = $response->json();
    $text = data_get($json, 'candidates.0.content.parts.0.text');

    if (empty($text)) {
        throw new RuntimeException('Gemini no devolvió contenido.');
    }

    $parsed = $this->parseExtraFields($text);

    // Las URLs de Spotify/YouTube vienen de las fuentes REALES que usó Grounding,
    // no de texto generado por el modelo — así evitamos alucinaciones.
   $sources = data_get($json, 'candidates.0.groundingMetadata.groundingChunks', []);
    \Illuminate\Support\Facades\Log::info('groundingChunks recibidos', ['count' => count($sources), 'sources' => $sources]);

    $spotifyUrl = null;
    $videos = [];

    foreach ($sources as $source) {
        $redirectUri = data_get($source, 'web.uri');
        if (! $redirectUri) {
            continue;
        }

        // Los URIs de groundingChunks son links de redirección de Google, no la URL real.
        // Hay que resolver el redirect para saber a dónde apuntan de verdad.
        $realUrl = $this->resolveRedirect($redirectUri);
        if (! $realUrl) {
            continue;
        }

        if (! $spotifyUrl && str_contains($realUrl, 'open.spotify.com')) {
            $spotifyUrl = $realUrl;
        }

        if (count($videos) < 2 && (str_contains($realUrl, 'youtube.com/watch') || str_contains($realUrl, 'youtu.be/'))) {
            $videos[] = $realUrl;
        }

        if ($spotifyUrl && count($videos) >= 2) {
            break;
        }
    }

    $parsed['spotify_url'] = $spotifyUrl ?? '';
    $parsed['videos'] = $videos;

    return $parsed;
}

public function generateStudioPortrait(string $imageBinary, string $mimeType = 'image/webp'): string
{
    $prompt = <<<PROMPT
Edit this photo to create a professional studio portrait while strictly preserving 
the exact identity, facial features, body proportions, skin tone, and physical 
likeness of the person(s) shown — do not alter or beautify their face, do not 
change their ethnicity, age, or body type.
Background requirements:
- Replace the background with a professional photography cyclorama backdrop in 
  a dark warm gray color (#1E1A17), with a subtle, smooth gradient — slightly 
  lighter around the subject's mid-height and gradually darkening toward the 
  edges and corners (classic studio vignette falloff).
- Add a very subtle fine-grain texture to the backdrop, like a genuine paper 
  or fabric cyclorama surface — not a flat digital color, but not distracting 
  or noisy either.
- The backdrop must look like a real seamless studio cyclorama (curved floor-to-wall 
  transition feel), consistent across all edited images so they form a cohesive set.
Subject requirements:
- Keep the full body or full framing visible as originally composed — do NOT 
  crop, cut off, or zoom into any part of the body, hands, feet, or head that 
  was visible in the original image.
- If there are multiple people in the photo, preserve all of them, their exact 
  positions relative to each other, and their individual identities.
- Remove any watermarks, text overlays, logos, frames, borders, date stamps, 
  or graphic elements added on top of the original photo.
- Preserve original clothing, hairstyle, expression, and pose exactly as is.
Lighting requirements:
- Relight the subject(s) to match professional studio photography: soft, even 
  key lighting with subtle rim/edge lighting to separate them from the 
  cyclorama background.
- Enhance image sharpness and clarity naturally, remove noise/grain and JPEG 
  compression artifacts, without smoothing skin texture in an artificial way.
Output should look like a cohesive, high-end studio photoshoot image, consistent 
in style with other images edited using this same treatment (same backdrop color, 
gradient, and texture).
Do not add any new people, objects, props, or text. Do not change the aspect 
ratio unless necessary to avoid cropping the subject.
PROMPT;

    $response = Http::withHeaders([
        'x-goog-api-key' => config('services.gemini.key'),
        'Content-Type' => 'application/json',
    ])->timeout(120)->post(
        'https://generativelanguage.googleapis.com/v1beta/models/' . config('services.gemini.image_model') . ':generateContent',
        [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt],
                        ['inline_data' => [
                            'mime_type' => $mimeType,
                            'data' => base64_encode($imageBinary),
                        ]],
                    ],
                ],
            ],
            'generationConfig' => [
                'responseModalities' => ['TEXT', 'IMAGE'],
            ],
        ]
    );

    if ($response->failed()) {
        throw new RuntimeException('Error al generar retrato de estudio con Gemini: ' . $response->body());
    }

    $parts = data_get($response->json(), 'candidates.0.content.parts', []);

    foreach ($parts as $part) {
        $data = data_get($part, 'inlineData.data') ?? data_get($part, 'inline_data.data');
        if (! empty($data)) {
            return base64_decode($data);
        }
    }

    throw new RuntimeException('Gemini no devolvió ninguna imagen editada.');
}

    protected function parseExtraFields(string $text): array
    {
        $pattern = '/===(SUMMARY|HIGHLIGHTS)===\s*(.*?)(?=(===[A-Z_]+===|$))/s';
        preg_match_all($pattern, $text, $matches, PREG_SET_ORDER);

        $raw = [];
        foreach ($matches as $match) {
            $raw[strtolower($match[1])] = trim($match[2]);
        }

        $highlights = [];
        if (! empty($raw['highlights'])) {
            foreach (explode("\n", $raw['highlights']) as $line) {
                $line = trim(ltrim(trim($line), '- '));
                if ($line !== '') {
                    $highlights[] = $line;
                }
            }
        }

        return [
            'summary' => $raw['summary'] ?? '',
            'highlights' => $highlights,
        ];
    }
    protected function resolveRedirect(string $url, int $maxHops = 5): ?string
    {
        for ($i = 0; $i < $maxHops; $i++) {
            try {
                $response = Http::withOptions(['allow_redirects' => false])->timeout(10)->get($url);
            } catch (\Throwable $e) {
                return null;
            }

            $location = $response->header('Location');

            if ($response->status() >= 300 && $response->status() < 400 && $location) {
                $url = $location;
                continue;
            }

            return $url;
        }

        return $url;
    }
}
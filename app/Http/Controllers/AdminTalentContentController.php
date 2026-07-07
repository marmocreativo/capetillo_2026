<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Talent;
use App\Services\GeminiContentService;
use Illuminate\Http\JsonResponse;

class AdminTalentContentController extends Controller
{
    public function generate(\Illuminate\Http\Request $request, Talent $talent, GeminiContentService $gemini): JsonResponse
    {
        $categoryNames = $talent->categories()->pluck('name')->toArray();

        try {
            $data = $gemini->generateTalentContent($talent->name, $categoryNames);
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        if ($request->boolean('save')) {
            $talent->update($data);
        }

        return response()->json($data);
    }

    public function allIds(\Illuminate\Http\Request $request): JsonResponse
    {
        $query = Talent::query();

        if ($request->boolean('only_empty')) {
            $field = $request->input('field', 'content');

            $query->where(function ($q) use ($field) {
                $q->whereNull($field)->orWhere($field, '');
            });
        }

        if ($request->boolean('with_cover_image')) {
            $query->whereNotNull('cover_image')->where('cover_image', '!=', '');
        }

        return response()->json($query->pluck('slug'));
    }

    public function generateExtra(\Illuminate\Http\Request $request, Talent $talent, GeminiContentService $gemini): JsonResponse
    {
        $categoryNames = $talent->categories()->pluck('name')->toArray();

        try {
            $data = $gemini->generateExtraFields($talent->name, $categoryNames);
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        if ($request->boolean('save')) {
            $update = [];
            if (! empty($data['summary'])) {
                $update['summary'] = $data['summary'];
            }
            if (! empty($data['highlights'])) {
                $update['highlights'] = $data['highlights'];
            }
            if (! empty($data['spotify_url'])) {
                $update['spotify_url'] = $data['spotify_url'];
            }
            if (! empty($update)) {
                $talent->update($update);
            }

            if (! empty($data['videos'])) {
                $talent->videos()->delete();
                foreach ($data['videos'] as $index => $url) {
                    $youtubeId = $this->extractYoutubeIdFromUrl($url);
                    if ($youtubeId) {
                        $talent->videos()->create(['youtube_id' => $youtubeId, 'order' => $index]);
                    }
                }
            }
        }

        return response()->json($data);
    }

    protected function extractYoutubeIdFromUrl(string $url): ?string
    {
        if (preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $url, $matches)) {
            return $matches[1];
        }

        return null;
    }

    public function generateStudioImage(Talent $talent, GeminiContentService $gemini, \App\Services\ImageUploadService $imageUpload): JsonResponse
{
    if (! $talent->cover_image || ! \Illuminate\Support\Facades\Storage::disk('public')->exists($talent->cover_image)) {
        return response()->json(['message' => 'El talento no tiene imagen de portada para editar.'], 422);
    }

    try {
        $binary = \Illuminate\Support\Facades\Storage::disk('public')->get($talent->cover_image);
        $edited = $gemini->generateStudioPortrait($binary, 'image/webp');

        $oldImage = $talent->cover_image;
        $newPath = $imageUpload->storeFromBinary($edited, 'talents');

        $talent->update(['cover_image' => $newPath]);

        \Illuminate\Support\Facades\Storage::disk('public')->delete($oldImage);
    } catch (\Throwable $e) {
        return response()->json(['message' => $e->getMessage()], 422);
    }

    return response()->json([
        'cover_image_url' => \Illuminate\Support\Facades\Storage::url($talent->cover_image),
    ]);
}
}
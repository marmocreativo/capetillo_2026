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
}
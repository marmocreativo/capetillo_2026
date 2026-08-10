<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Services\GeminiContentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminEventContentController extends Controller
{
    public function generate(Request $request, Event $event, GeminiContentService $gemini): JsonResponse
    {
        $ciudad = $request->input('ciudad', 'CDMX');

        try {
            $data = $gemini->generateEventContent($event->title, $ciudad);
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        if ($request->boolean('save')) {
            $event->update($data);
        }

        return response()->json($data);
    }
}
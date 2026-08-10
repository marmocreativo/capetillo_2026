<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Talent;

class EventController extends Controller
{
    public function show(Event $event)
    {
        abort_unless($event->is_active, 404);

        $event->load('images', 'videos');

        $randomTalents = Talent::where('is_active', true)
            ->whereNotNull('cover_image')
            ->inRandomOrder()
            ->limit(12)
            ->get();

        return view('public.events.show', compact('event', 'randomTalents'));
    }
}
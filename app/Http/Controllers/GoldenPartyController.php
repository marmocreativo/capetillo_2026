<?php

namespace App\Http\Controllers;

use App\Models\Event;

class GoldenPartyController extends Controller
{
    public function index()
    {
        $events = Event::where('is_active', true)
            ->orderBy('orden')
            ->orderBy('title')
            ->get();

        return view('public.golden-party', compact('events'));
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Roster;
use App\Services\RosterPresentationService;

class AdminRosterExportController extends Controller
{
    public function export(Roster $roster, RosterPresentationService $service)
    {
        $path = $service->generate($roster);

        $filename = \Illuminate\Support\Str::slug($roster->name) . '.pptx';

        return response()->download($path, $filename, [
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ])->deleteFileAfterSend(true);
    }
}
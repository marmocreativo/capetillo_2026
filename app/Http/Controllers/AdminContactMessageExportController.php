<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Services\ContactMessagePresentationService;

class AdminContactMessageExportController extends Controller
{
    public function export(ContactMessage $contactMessage, ContactMessagePresentationService $service)
    {
        $path = $service->generate($contactMessage);

        $filename = 'cotizacion-' . str_pad($contactMessage->id, 5, '0', STR_PAD_LEFT) . '.pptx';

        return response()->download($path, $filename)->deleteFileAfterSend(true);
    }
}
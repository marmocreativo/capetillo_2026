<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLogoRosterRequest;
use App\Models\LogoRoster;
use App\Services\ImageUploadService;
use Illuminate\Support\Facades\Storage;

class AdminLogoRosterController extends Controller
{
    public function __construct(protected ImageUploadService $imageUploadService)
    {
    }

    public function index()
    {
        $logosRoster = LogoRoster::latest()->paginate(24);

        return view('admin.logos-roster.index', compact('logosRoster'));
    }

    public function store(StoreLogoRosterRequest $request)
    {
        $path = $this->imageUploadService->storeMaxWidth($request->file('image'), 'logos-roster');

        LogoRoster::create(['image' => $path]);

        return redirect()->route('admin.logos-roster.index')->with('status', 'Logo subido correctamente.');
    }

    public function destroy(LogoRoster $logosRoster)
    {
        Storage::disk('public')->delete($logosRoster->image);
        $logosRoster->delete();

        return redirect()->route('admin.logos-roster.index')->with('status', 'Logo eliminado correctamente.');
    }
}
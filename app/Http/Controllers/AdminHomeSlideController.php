<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHomeSlideRequest;
use App\Models\HomeSlide;
use App\Services\ImageUploadService;
use Illuminate\Support\Facades\Storage;

class AdminHomeSlideController extends Controller
{
    public function __construct(protected ImageUploadService $imageUploadService)
    {
    }

    public function index()
    {
        $homeSlides = HomeSlide::orderBy('order')->orderBy('id')->paginate(15);

        return view('admin.home-slides.index', compact('homeSlides'));
    }

    public function create()
    {
        return view('admin.home-slides.create');
    }

    public function store(StoreHomeSlideRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            $data['image'] = $this->imageUploadService->storeTransparent($request->file('image'), 'home-slides');
        }

        HomeSlide::create($data);

        return redirect()->route('admin.home-slides.index')->with('status', 'Slide creado correctamente.');
    }

    public function edit(HomeSlide $homeSlide)
    {
        return view('admin.home-slides.edit', compact('homeSlide'));
    }

    public function update(StoreHomeSlideRequest $request, HomeSlide $homeSlide)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            if ($homeSlide->image) {
                Storage::disk('public')->delete($homeSlide->image);
            }
            $data['image'] = $this->imageUploadService->storeTransparent($request->file('image'), 'home-slides');
        }

        $homeSlide->update($data);

        return redirect()->route('admin.home-slides.index')->with('status', 'Slide actualizado correctamente.');
    }

    public function destroy(HomeSlide $homeSlide)
    {
        if ($homeSlide->image) {
            Storage::disk('public')->delete($homeSlide->image);
        }

        $homeSlide->delete();

        return redirect()->route('admin.home-slides.index')->with('status', 'Slide eliminado correctamente.');
    }
}
<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTalentRequest;
use App\Models\Category;
use App\Models\Talent;
use App\Services\ImageUploadService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminTalentController extends Controller
{
    public function __construct(protected ImageUploadService $imageUploadService)
    {
    }

    

    public function index(\Illuminate\Http\Request $request)
    {
        $query = Talent::with('categories');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories.slug', $request->input('category'));
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->input('status') === 'active');
        }

        $perPage = (int) $request->input('per_page', 15);
        $perPage = in_array($perPage, [15, 50, 100, 250], true) ? $perPage : 15;

        $talents = $query->orderBy('orden')
            ->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();

        return view('admin.talents.index', compact('talents', 'categories'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.talents.create', compact('categories'));
    }

    public function store(StoreTalentRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        $data['is_active'] = $request->boolean('is_active');
        $data['destacado'] = $request->boolean('destacado');
        $data['orden'] = $data['orden'] ?? ((int) Talent::max('orden') + 1);
        $data['highlights'] = array_values(array_filter($request->input('highlights', [])));
        $data['mostrar_network'] = $request->boolean('mostrar_network');
        $data['mostrar_party'] = $request->boolean('mostrar_party');
        $data['recomendaciones_network'] = array_values(array_filter($request->input('recomendaciones_network', [])));
        $data['recomendaciones_party'] = array_values(array_filter($request->input('recomendaciones_party', [])));

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $this->imageUploadService->store($request->file('cover_image'), 'talents');
        }

        $talent = Talent::create(collect($data)->except(['new_images', 'delete_images', 'videos'])->toArray());
        $talent->categories()->sync($data['categories']);

        $this->syncGallery($request, $talent);
        $this->syncVideos($request, $talent);

        return redirect()->route('admin.talents.index')->with('status', 'Talento creado correctamente.');
    }

    public function update(StoreTalentRequest $request, Talent $talent)
    {
        $data = $request->validated();
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        $data['is_active'] = $request->boolean('is_active');
        $data['destacado'] = $request->boolean('destacado');
        $data['orden'] = $data['orden'] ?? $talent->orden;
        $data['highlights'] = array_values(array_filter($request->input('highlights', [])));
        $data['mostrar_network'] = $request->boolean('mostrar_network');
        $data['mostrar_party'] = $request->boolean('mostrar_party');
        $data['recomendaciones_network'] = array_values(array_filter($request->input('recomendaciones_network', [])));
        $data['recomendaciones_party'] = array_values(array_filter($request->input('recomendaciones_party', [])));

        if ($request->hasFile('cover_image')) {
            if ($talent->cover_image) {
                Storage::disk('public')->delete($talent->cover_image);
            }
            $data['cover_image'] = $this->imageUploadService->store($request->file('cover_image'), 'talents');
        }

        $talent->update(collect($data)->except(['new_images', 'delete_images', 'videos'])->toArray());
        $talent->categories()->sync($data['categories']);

        $this->syncGallery($request, $talent);
        $this->syncVideos($request, $talent);

        return redirect()->route('admin.talents.index')->with('status', 'Talento actualizado correctamente.');
    }

    protected function syncGallery(\Illuminate\Http\Request $request, Talent $talent): void
    {
        $deleteIds = $request->input('delete_images', []);

        if (! empty($deleteIds)) {
            $images = $talent->images()->whereIn('id', $deleteIds)->get();
            foreach ($images as $image) {
                Storage::disk('public')->delete($image->path);
                $image->delete();
            }
        }

        if ($request->hasFile('new_images')) {
            $order = $talent->images()->max('order') ?? 0;
            foreach ($request->file('new_images') as $file) {
                $order++;
                $path = $this->imageUploadService->store($file, 'talents/gallery');
                $talent->images()->create(['path' => $path, 'order' => $order]);
            }
        }
    }

    protected function syncVideos(\Illuminate\Http\Request $request, Talent $talent): void
    {
        $entries = array_values(array_filter($request->input('videos', [])));

        $talent->videos()->delete();

        foreach ($entries as $index => $entry) {
            $youtubeId = $this->extractYoutubeId($entry);
            if ($youtubeId) {
                $talent->videos()->create(['youtube_id' => $youtubeId, 'order' => $index]);
            }
        }
    }

    protected function extractYoutubeId(string $value): ?string
    {
        if (preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $value, $matches)) {
            return $matches[1];
        }

        if (preg_match('/^[a-zA-Z0-9_-]{11}$/', trim($value))) {
            return trim($value);
        }

        return null;
    }

    public function edit(Talent $talent)
    {
        $categories = Category::orderBy('name')->get();
        $talent->load('categories');

        return view('admin.talents.edit', compact('talent', 'categories'));
    }


    public function destroy(Talent $talent)
    {
        if ($talent->cover_image) {
            Storage::disk('public')->delete($talent->cover_image);
        }

        $talent->delete();

        return redirect()->route('admin.talents.index')->with('status', 'Talento eliminado correctamente.');
    }

    public function toggleActive(Talent $talent)
    {
        $talent->update(['is_active' => ! $talent->is_active]);

        return response()->json(['is_active' => $talent->is_active]);
    }

    public function toggleDestacado(Talent $talent)
    {
        $talent->update(['destacado' => ! $talent->destacado]);

        return response()->json(['destacado' => $talent->destacado]);
    }

    public function updateHonorarios(\Illuminate\Http\Request $request, Talent $talent)
    {
        $data = $request->validate([
            'honorarios_default' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
        ]);

        $talent->update($data);

        return response()->json([
            'honorarios_default' => $talent->honorarios_default,
            'formatted' => $talent->honorarios_default !== null
                ? '$' . number_format((float) $talent->honorarios_default, 2)
                : '—',
        ]);
    }

    public function reorder(\Illuminate\Http\Request $request)
    {
        $slugs = $request->input('order', []);

        foreach ($slugs as $index => $slug) {
            Talent::where('slug', $slug)->update(['orden' => $index]);
        }

        return response()->json(['status' => 'ok']);
    }
}
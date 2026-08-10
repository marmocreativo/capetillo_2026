<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventRequest;
use App\Models\Event;
use App\Services\ImageUploadService;
use Illuminate\Support\Facades\Storage;

class AdminEventController extends Controller
{
    public function __construct(protected ImageUploadService $imageUploadService)
    {
    }

    public function index(\Illuminate\Http\Request $request)
    {
        $query = Event::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->input('status') === 'active');
        }

        $events = $query->orderBy('orden')->orderBy('title')
            ->paginate(15)
            ->withQueryString();

        return view('admin.events.index', compact('events'));
    }

    public function create()
    {
        return view('admin.events.create');
    }

    public function store(StoreEventRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = $data['slug'] ?? Event::buildSlug($data['title']);
        $data['is_active'] = $request->boolean('is_active');
        $data['orden'] = $data['orden'] ?? ((int) Event::max('orden') + 1);
        $data['servicios_especializados'] = $this->buildServicios($request);
        $data['preguntas_frecuentes'] = $this->buildFaqs($request);

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $this->imageUploadService->store($request->file('cover_image'), 'events');
        }

        $event = Event::create(collect($data)->except(['new_images', 'delete_images', 'videos', 'servicio_titulo', 'servicio_descripcion', 'faq_pregunta', 'faq_respuesta'])->toArray());

        $this->syncGallery($request, $event);
        $this->syncVideos($request, $event);

        return redirect()->route('admin.events.index')->with('status', 'Evento creado correctamente.');
    }

    public function edit(Event $event)
    {
        $event->load('images', 'videos');

        return view('admin.events.edit', compact('event'));
    }

    public function update(StoreEventRequest $request, Event $event)
    {
        $data = $request->validated();
        $data['slug'] = $data['slug'] ?? $event->slug;
        $data['is_active'] = $request->boolean('is_active');
        $data['orden'] = $data['orden'] ?? $event->orden;
        $data['servicios_especializados'] = $this->buildServicios($request);
        $data['preguntas_frecuentes'] = $this->buildFaqs($request);

        if ($request->hasFile('cover_image')) {
            if ($event->cover_image) {
                Storage::disk('public')->delete($event->cover_image);
            }
            $data['cover_image'] = $this->imageUploadService->store($request->file('cover_image'), 'events');
        }

        $event->update(collect($data)->except(['new_images', 'delete_images', 'videos', 'servicio_titulo', 'servicio_descripcion', 'faq_pregunta', 'faq_respuesta'])->toArray());

        $this->syncGallery($request, $event);
        $this->syncVideos($request, $event);

        return redirect()->route('admin.events.index')->with('status', 'Evento actualizado correctamente.');
    }

    protected function buildServicios(\Illuminate\Http\Request $request): array
    {
        $titulos = $request->input('servicio_titulo', []);
        $descripciones = $request->input('servicio_descripcion', []);

        $servicios = [];
        foreach ($titulos as $index => $titulo) {
            $titulo = trim((string) $titulo);
            $descripcion = trim((string) ($descripciones[$index] ?? ''));

            if ($titulo !== '' || $descripcion !== '') {
                $servicios[] = [
                    'titulo_servicio' => $titulo,
                    'descripcion' => $descripcion,
                ];
            }
        }

        return $servicios;
    }

    protected function buildFaqs(\Illuminate\Http\Request $request): array
    {
        $preguntas = $request->input('faq_pregunta', []);
        $respuestas = $request->input('faq_respuesta', []);

        $faqs = [];
        foreach ($preguntas as $index => $pregunta) {
            $pregunta = trim((string) $pregunta);
            $respuesta = trim((string) ($respuestas[$index] ?? ''));

            if ($pregunta !== '' || $respuesta !== '') {
                $faqs[] = [
                    'pregunta' => $pregunta,
                    'respuesta' => $respuesta,
                ];
            }
        }

        return $faqs;
    }

    public function destroy(Event $event)
    {
        if ($event->cover_image) {
            Storage::disk('public')->delete($event->cover_image);
        }

        foreach ($event->images as $image) {
            Storage::disk('public')->delete($image->path);
        }

        $event->delete();

        return redirect()->route('admin.events.index')->with('status', 'Evento eliminado correctamente.');
    }

    protected function syncGallery(\Illuminate\Http\Request $request, Event $event): void
    {
        $deleteIds = $request->input('delete_images', []);

        if (! empty($deleteIds)) {
            $images = $event->images()->whereIn('id', $deleteIds)->get();
            foreach ($images as $image) {
                Storage::disk('public')->delete($image->path);
                $image->delete();
            }
        }

        if ($request->hasFile('new_images')) {
            $order = $event->images()->max('order') ?? 0;
            foreach ($request->file('new_images') as $file) {
                $order++;
                $path = $this->imageUploadService->store($file, 'events/gallery');
                $event->images()->create(['path' => $path, 'order' => $order]);
            }
        }
    }

    protected function syncVideos(\Illuminate\Http\Request $request, Event $event): void
    {
        $entries = array_values(array_filter($request->input('videos', [])));

        $event->videos()->delete();

        foreach ($entries as $index => $entry) {
            $youtubeId = $this->extractYoutubeId($entry);
            if ($youtubeId) {
                $event->videos()->create(['youtube_id' => $youtubeId, 'order' => $index]);
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
}
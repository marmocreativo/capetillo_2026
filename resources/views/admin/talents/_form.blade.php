@if ($errors->any())
    <div class="alert alert-error mb-4">
        <ul class="list-disc list-inside text-sm">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@php
    $selectedCategories = old('categories', isset($talent) ? $talent->categories->pluck('id')->toArray() : []);
@endphp

<div role="tablist" class="tabs tabs-lift">

    {{-- TAB 1: General --}}
    <input type="radio" name="talent-tabs" role="tab" class="tab" aria-label="General" checked="checked">
    <div role="tabpanel" class="tab-content bg-base-100 border-base-300 p-6">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Columna izquierda: contenido principal --}}
            <div class="lg:col-span-2 flex flex-col gap-4">

                <div class="form-control">
                    <label class="label"><span class="label-text">Nombre</span></label>
                    <input type="text" name="name" value="{{ old('name', $talent->name ?? '') }}" class="input input-bordered w-full" required>
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text">Slug (déjalo vacío para autogenerar)</span></label>
                    <input type="text" name="slug" value="{{ old('slug', $talent->slug ?? '') }}" class="input input-bordered w-full">
                </div>

                <div class="form-control">
                    <div class="flex justify-between items-center mb-1">
                        <label class="label"><span class="label-text">Descripción / biografía (HTML)</span></label>
                        @if (isset($talent) && $talent->exists)
                            <button type="button" id="generate-ai-btn" class="btn btn-xs btn-outline btn-secondary"
                                data-url="{{ route('admin.talents.generate-content', $talent) }}">
                                ✨ Generar con IA
                            </button>
                        @endif
                    </div>
                    <textarea name="content" id="content-field" class="textarea textarea-bordered w-full font-mono text-sm" rows="10">{{ old('content', $talent->content ?? '') }}</textarea>
                    <p class="text-xs opacity-60 mt-1">La IA investiga y redacta la biografía. Revisa y edita el resultado antes de guardar.</p>
                </div>

                <div class="form-control">
                    <div class="flex justify-between items-center mb-1">
                        <label class="label"><span class="label-text">Resumen corto</span></label>
                        @if (isset($talent) && $talent->exists)
                            <button type="button" id="generate-extra-btn" class="btn btn-xs btn-outline btn-secondary"
                                data-url="{{ route('admin.talents.generate-extra', $talent) }}">
                                🔍 Buscar campos extra con IA
                            </button>
                        @endif
                    </div>
                    <input type="text" name="summary" id="summary-field" value="{{ old('summary', $talent->summary ?? '') }}" class="input input-bordered w-full" maxlength="500">
                    <p class="text-xs opacity-60 mt-1">⚠️ Los links de Spotify/YouTube que sugiera la IA pueden ser incorrectos — verifícalos en la pestaña "Multimedia y logros" antes de guardar.</p>
                </div>

                <div class="divider my-0">SEO</div>

                <div class="form-control">
                    <label class="label"><span class="label-text">Meta título</span></label>
                    <input type="text" name="meta_title" value="{{ old('meta_title', $talent->meta_title ?? '') }}" class="input input-bordered w-full">
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text">Meta descripción</span></label>
                    <textarea name="meta_description" class="textarea textarea-bordered w-full" rows="3">{{ old('meta_description', $talent->meta_description ?? '') }}</textarea>
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text">Meta keywords</span></label>
                    <input type="text" name="meta_keywords" value="{{ old('meta_keywords', $talent->meta_keywords ?? '') }}" class="input input-bordered w-full">
                </div>

            </div>

            {{-- Columna derecha: imagen, estado, categorías --}}
            <div class="lg:col-span-1">
                <div class="card bg-base-100 border border-base-300 lg:sticky lg:top-6">
                    <div class="card-body gap-4">

                        <div class="form-control">
                            <label class="label"><span class="label-text">Imagen de portada (4:5)</span></label>
                            <input type="file" name="cover_image" accept="image/*" class="file-input file-input-bordered w-full" onchange="previewCoverImage(event)">
                            <div class="mt-2">
                                <img id="cover-preview"
                                     src="{{ isset($talent) && $talent->cover_image ? Storage::url($talent->cover_image) : '' }}"
                                     class="w-full aspect-[4/5] object-cover rounded {{ isset($talent) && $talent->cover_image ? '' : 'hidden' }}">
                            </div>
                        </div>

                        <label class="label cursor-pointer justify-start gap-2">
                            <input type="checkbox" name="is_active" value="1" class="checkbox" {{ old('is_active', $talent->is_active ?? true) ? 'checked' : '' }}>
                            <span class="label-text">Talento activo</span>
                        </label>

                        <div class="divider my-0"></div>

                        <div class="form-control">
                            <label class="label"><span class="label-text">Categorías</span></label>
                            <div class="flex flex-col gap-1 p-3 border border-base-300 rounded-box max-h-64 overflow-y-auto">
                                @foreach ($categories as $category)
                                    <label class="label cursor-pointer gap-2 justify-start py-1">
                                        <input type="checkbox" name="categories[]" value="{{ $category->id }}" class="checkbox checkbox-sm"
                                            {{ in_array($category->id, $selectedCategories) ? 'checked' : '' }}>
                                        <span class="label-text">{{ $category->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- TAB 2: Galería --}}
    <input type="radio" name="talent-tabs" role="tab" class="tab" aria-label="Galería">
    <div role="tabpanel" class="tab-content bg-base-100 border-base-300 p-6">

        <div class="form-control">
            <label class="label"><span class="label-text">Galería de imágenes</span></label>

            @if (isset($talent) && $talent->exists && $talent->images->isNotEmpty())
                <div class="grid grid-cols-3 sm:grid-cols-6 gap-3 mb-3">
                    @foreach ($talent->images as $image)
                        <div class="relative">
                            <img src="{{ Storage::url($image->path) }}" class="w-full aspect-square object-cover rounded">
                            <label class="absolute top-1 right-1 bg-base-100/90 rounded px-1 text-xs flex items-center gap-1 cursor-pointer">
                                <input type="checkbox" name="delete_images[]" value="{{ $image->id }}" class="checkbox checkbox-xs checkbox-error">
                                Eliminar
                            </label>
                        </div>
                    @endforeach
                </div>
            @endif

            <input type="file" name="new_images[]" accept="image/*" multiple class="file-input file-input-bordered w-full">
            <p class="text-xs opacity-60 mt-1">Puedes seleccionar varias imágenes a la vez. Se agregan a la galería existente.</p>
        </div>

    </div>

    {{-- TAB 3: Multimedia y logros --}}
    <input type="radio" name="talent-tabs" role="tab" class="tab" aria-label="Multimedia y logros">
    <div role="tabpanel" class="tab-content bg-base-100 border-base-300 p-6">

        <div class="flex flex-col gap-4">

            <div class="form-control">
                <label class="label"><span class="label-text">Link de playlist de Spotify</span></label>
                <input type="url" name="spotify_url" value="{{ old('spotify_url', $talent->spotify_url ?? '') }}" class="input input-bordered w-full" placeholder="https://open.spotify.com/playlist/...">
            </div>

            <div class="form-control">
                <label class="label"><span class="label-text">Bullets de logros</span></label>
                <div id="highlights-repeater" class="flex flex-col gap-2">
                    @php $existingHighlights = old('highlights', $talent->highlights ?? []); @endphp
                    @forelse ($existingHighlights as $highlight)
                        <div class="flex gap-2">
                            <input type="text" name="highlights[]" value="{{ $highlight }}" class="input input-bordered input-sm w-full">
                            <button type="button" class="btn btn-sm btn-error btn-outline remove-row">✕</button>
                        </div>
                    @empty
                        <div class="flex gap-2">
                            <input type="text" name="highlights[]" value="" class="input input-bordered input-sm w-full">
                            <button type="button" class="btn btn-sm btn-error btn-outline remove-row">✕</button>
                        </div>
                    @endforelse
                </div>
                <button type="button" id="add-highlight" class="btn btn-xs btn-outline mt-2">+ Agregar logro</button>
            </div>

            <div class="form-control">
                <label class="label"><span class="label-text">Videos de YouTube (URL o ID)</span></label>
                <div id="videos-repeater" class="flex flex-col gap-2">
                    @php $existingVideos = old('videos', isset($talent) ? $talent->videos->pluck('youtube_id')->toArray() : []); @endphp
                    @forelse ($existingVideos as $video)
                        <div class="flex gap-2">
                            <input type="text" name="videos[]" value="{{ $video }}" class="input input-bordered input-sm w-full" placeholder="https://youtube.com/watch?v=...">
                            <button type="button" class="btn btn-sm btn-error btn-outline remove-row">✕</button>
                        </div>
                    @empty
                        <div class="flex gap-2">
                            <input type="text" name="videos[]" value="" class="input input-bordered input-sm w-full" placeholder="https://youtube.com/watch?v=...">
                            <button type="button" class="btn btn-sm btn-error btn-outline remove-row">✕</button>
                        </div>
                    @endforelse
                </div>
                <button type="button" id="add-video" class="btn btn-xs btn-outline mt-2">+ Agregar video</button>
            </div>

        </div>

    </div>

</div>

<div class="mt-6 flex gap-2">
    <button type="submit" class="btn btn-primary">Guardar</button>
    <a href="{{ route('admin.talents.index') }}" class="btn btn-ghost">Cancelar</a>
</div>

<script>
function previewCoverImage(event) {
    const preview = document.getElementById('cover-preview');
    const file = event.target.files[0];
    if (file) {
        preview.src = URL.createObjectURL(file);
        preview.classList.remove('hidden');
    }
}

const generateBtn = document.getElementById('generate-ai-btn');
if (generateBtn) {
    generateBtn.addEventListener('click', async () => {
        const originalText = generateBtn.textContent;
        generateBtn.disabled = true;
        generateBtn.textContent = 'Generando...';

        try {
            const response = await fetch(generateBtn.dataset.url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                        || document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json',
                },
            });

            const data = await response.json();

            if (!response.ok) {
                alert(data.message || 'Error al generar contenido.');
                return;
            }

            document.getElementById('content-field').value = data.content;
            document.querySelector('input[name="meta_title"]').value = data.meta_title;
            document.querySelector('textarea[name="meta_description"]').value = data.meta_description;
            document.querySelector('input[name="meta_keywords"]').value = data.meta_keywords;
        } catch (error) {
            alert('Error de conexión al generar contenido.');
        } finally {
            generateBtn.disabled = false;
            generateBtn.textContent = originalText;
        }
    });
}
const generateExtraBtn = document.getElementById('generate-extra-btn');
if (generateExtraBtn) {
    generateExtraBtn.addEventListener('click', async () => {
        const originalText = generateExtraBtn.textContent;
        generateExtraBtn.disabled = true;
        generateExtraBtn.textContent = 'Buscando...';

        try {
            const response = await fetch(generateExtraBtn.dataset.url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json',
                },
            });

            const data = await response.json();
            if (!response.ok) {
                alert(data.message || 'Error al buscar campos extra.');
                return;
            }

            document.getElementById('summary-field').value = data.summary || '';

            replaceRepeaterRows('highlights-repeater', 'highlights[]', data.highlights || []);
            replaceRepeaterRows('videos-repeater', 'videos[]', data.videos || [], 'https://youtube.com/watch?v=...');

            const spotifyInput = document.querySelector('input[name="spotify_url"]');
            if (data.spotify_url) {
                spotifyInput.value = data.spotify_url;
            }

            alert('Listo. Revisa el resumen, logros, y verifica manualmente los links de Spotify/YouTube antes de guardar.');
        } catch (error) {
            alert('Error de conexión al buscar campos extra.');
        } finally {
            generateExtraBtn.disabled = false;
            generateExtraBtn.textContent = originalText;
        }
    });
}

function replaceRepeaterRows(containerId, inputName, values, placeholder = '') {
    const container = document.getElementById(containerId);
    container.innerHTML = '';

    if (values.length === 0) {
        addRepeaterRow(containerId, inputName, placeholder);
        return;
    }

    values.forEach(value => {
        const row = document.createElement('div');
        row.className = 'flex gap-2';
        row.innerHTML = `
            <input type="text" name="${inputName}" value="${value.replace(/"/g, '&quot;')}" class="input input-bordered input-sm w-full" placeholder="${placeholder}">
            <button type="button" class="btn btn-sm btn-error btn-outline remove-row">✕</button>
        `;
        container.appendChild(row);
    });
}

function addRepeaterRow(containerId, inputName, placeholder = '') {
    const container = document.getElementById(containerId);
    const row = document.createElement('div');
    row.className = 'flex gap-2';
    row.innerHTML = `
        <input type="text" name="${inputName}" value="" class="input input-bordered input-sm w-full" placeholder="${placeholder}">
        <button type="button" class="btn btn-sm btn-error btn-outline remove-row">✕</button>
    `;
    container.appendChild(row);
}

document.getElementById('add-highlight')?.addEventListener('click', () => {
    addRepeaterRow('highlights-repeater', 'highlights[]');
});

document.getElementById('add-video')?.addEventListener('click', () => {
    addRepeaterRow('videos-repeater', 'videos[]', 'https://youtube.com/watch?v=...');
});

document.addEventListener('click', (e) => {
    if (e.target.classList.contains('remove-row')) {
        e.target.closest('.flex.gap-2').remove();
    }
});
</script>
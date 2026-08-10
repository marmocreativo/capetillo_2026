@csrf

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 flex flex-col gap-6">

        <div class="card bg-base-100 shadow p-6">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 mb-4">
                <h2 class="font-bold">Información general</h2>
                @if (isset($event) && $event->exists)
                    <div class="flex items-center gap-2">
                        <input type="text" id="ai-ciudad-field" value="CDMX" placeholder="Ciudad" class="input input-bordered input-xs w-24">
                        <button type="button" id="generate-ai-btn" class="btn btn-xs btn-outline btn-secondary"
                            data-url="{{ route('admin.events.generate-content', $event) }}">
                            ✨ Generar con IA
                        </button>
                    </div>
                @endif
            </div>

            <label class="label"><span class="label-text">Título</span></label>
            <input type="text" name="title" value="{{ old('title', $event->title ?? '') }}" class="input input-bordered w-full" required>
            @error('title') <p class="text-error text-sm mt-1">{{ $message }}</p> @enderror

            <label class="label mt-3"><span class="label-text">Slug / URL (opcional, se genera automáticamente)</span></label>
            <input type="text" name="slug" value="{{ old('slug', $event->slug ?? '') }}" placeholder="organizacion-de-bodas-en-cdmx" class="input input-bordered w-full">
            @error('slug') <p class="text-error text-sm mt-1">{{ $message }}</p> @enderror

            <label class="label mt-3"><span class="label-text">Resumen corto</span></label>
            <textarea name="summary" id="summary-field" rows="2" class="textarea textarea-bordered w-full">{{ old('summary', $event->summary ?? '') }}</textarea>
            @error('summary') <p class="text-error text-sm mt-1">{{ $message }}</p> @enderror

            <label class="label mt-3"><span class="label-text">Descripción larga (HTML permitido)</span></label>
            <textarea name="content" id="content-field" rows="10" class="textarea textarea-bordered w-full font-mono text-sm">{{ old('content', $event->content ?? '') }}</textarea>
            @error('content') <p class="text-error text-sm mt-1">{{ $message }}</p> @enderror
            <p class="text-xs opacity-60 mt-1">La IA investiga y redacta el contenido optimizado para SEO. Revisa y edita antes de guardar.</p>
        </div>

        <div class="card bg-base-100 shadow p-6">
            <h2 class="font-bold mb-4">Servicios especializados</h2>

            <div id="servicios-repeater" class="flex flex-col gap-3">
                @php $existingServicios = old('servicio_titulo') ? collect(old('servicio_titulo'))->map(fn ($t, $i) => ['titulo_servicio' => $t, 'descripcion' => old('servicio_descripcion')[$i] ?? '']) : collect($event->servicios_especializados ?? []); @endphp
                @forelse ($existingServicios as $servicio)
                    <div class="grid grid-cols-1 sm:grid-cols-[1fr_2fr_auto] gap-2 items-start border border-base-300 rounded-box p-3">
                        <input type="text" name="servicio_titulo[]" value="{{ $servicio['titulo_servicio'] ?? '' }}" placeholder="Título del servicio" class="input input-bordered input-sm w-full">
                        <textarea name="servicio_descripcion[]" rows="2" placeholder="Descripción" class="textarea textarea-bordered textarea-sm w-full">{{ $servicio['descripcion'] ?? '' }}</textarea>
                        <button type="button" class="btn btn-sm btn-error btn-outline" onclick="this.closest('.grid').remove()">✕</button>
                    </div>
                @empty
                @endforelse
            </div>

            <button type="button" id="add-servicio" class="btn btn-xs btn-outline mt-3">+ Agregar servicio</button>
        </div>

        <div class="card bg-base-100 shadow p-6">
            <h2 class="font-bold mb-4">Preguntas frecuentes</h2>

            <div id="faqs-repeater" class="flex flex-col gap-3">
                @php $existingFaqs = old('faq_pregunta') ? collect(old('faq_pregunta'))->map(fn ($p, $i) => ['pregunta' => $p, 'respuesta' => old('faq_respuesta')[$i] ?? '']) : collect($event->preguntas_frecuentes ?? []); @endphp
                @forelse ($existingFaqs as $faq)
                    <div class="grid grid-cols-1 sm:grid-cols-[1fr_2fr_auto] gap-2 items-start border border-base-300 rounded-box p-3">
                        <input type="text" name="faq_pregunta[]" value="{{ $faq['pregunta'] ?? '' }}" placeholder="Pregunta" class="input input-bordered input-sm w-full">
                        <textarea name="faq_respuesta[]" rows="2" placeholder="Respuesta" class="textarea textarea-bordered textarea-sm w-full">{{ $faq['respuesta'] ?? '' }}</textarea>
                        <button type="button" class="btn btn-sm btn-error btn-outline" onclick="this.closest('.grid').remove()">✕</button>
                    </div>
                @empty
                @endforelse
            </div>

            <button type="button" id="add-faq" class="btn btn-xs btn-outline mt-3">+ Agregar pregunta</button>
        </div>

        <div class="card bg-base-100 shadow p-6">
            <h2 class="font-bold mb-4">SEO</h2>

            <label class="label"><span class="label-text">Meta título</span></label>
            <input type="text" name="meta_title" id="meta-title-field" value="{{ old('meta_title', $event->meta_title ?? '') }}" class="input input-bordered w-full">

            <label class="label mt-3"><span class="label-text">Meta descripción</span></label>
            <textarea name="meta_description" id="meta-description-field" rows="3" class="textarea textarea-bordered w-full">{{ old('meta_description', $event->meta_description ?? '') }}</textarea>

            <label class="label mt-3"><span class="label-text">Meta keywords</span></label>
            <input type="text" name="meta_keywords" id="meta-keywords-field" value="{{ old('meta_keywords', $event->meta_keywords ?? '') }}" class="input input-bordered w-full">
        </div>

        <div class="card bg-base-100 shadow p-6">
            <h2 class="font-bold mb-4">Galería de imágenes</h2>

            @isset($event)
                @if ($event->images->count())
                    <div class="grid grid-cols-3 sm:grid-cols-4 gap-3 mb-4">
                        @foreach ($event->images as $image)
                            <label class="relative block cursor-pointer">
                                <img src="{{ Storage::url($image->path) }}" class="w-full aspect-square object-cover rounded">
                                <input type="checkbox" name="delete_images[]" value="{{ $image->id }}" class="checkbox checkbox-error absolute top-1 right-1 bg-base-100">
                            </label>
                        @endforeach
                    </div>
                    <p class="text-xs opacity-60 mb-3">Marca las imágenes que quieras eliminar.</p>
                @endif
            @endisset

            <input type="file" name="new_images[]" multiple accept="image/*" class="file-input file-input-bordered w-full">
        </div>

        <div class="card bg-base-100 shadow p-6">
            <h2 class="font-bold mb-4">Videos de YouTube</h2>
            <div id="videos-wrapper" class="flex flex-col gap-2">
                @php $existingVideos = old('videos', isset($event) ? $event->videos->pluck('youtube_id')->toArray() : []); @endphp
                @forelse ($existingVideos as $video)
                    <div class="flex gap-2">
                        <input type="text" name="videos[]" value="{{ $video }}" placeholder="URL o ID de YouTube" class="input input-bordered w-full">
                        <button type="button" class="btn btn-error btn-outline" onclick="this.parentElement.remove()">Quitar</button>
                    </div>
                @empty
                @endforelse
            </div>
            <button type="button" class="btn btn-sm btn-neutral mt-3" onclick="addVideoField()">Agregar video</button>
        </div>
    </div>

    <div class="flex flex-col gap-6">
        <div class="card bg-base-100 shadow p-6">
            <h2 class="font-bold mb-4">Portada</h2>
            @isset($event)
                @if ($event->cover_image)
                    <img src="{{ Storage::url($event->cover_image) }}" class="w-full aspect-[4/5] object-cover rounded mb-3">
                @endif
            @endisset
            <input type="file" name="cover_image" accept="image/*" class="file-input file-input-bordered w-full">
            @error('cover_image') <p class="text-error text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="card bg-base-100 shadow p-6">
            <h2 class="font-bold mb-4">Publicación</h2>

            <label class="label"><span class="label-text">Orden</span></label>
            <input type="number" name="orden" value="{{ old('orden', $event->orden ?? 0) }}" class="input input-bordered w-full">

            <label class="label cursor-pointer justify-start gap-3 mt-3">
                <input type="checkbox" name="is_active" value="1" class="checkbox" {{ old('is_active', $event->is_active ?? true) ? 'checked' : '' }}>
                <span class="label-text">Activo</span>
            </label>
        </div>

        <button type="submit" class="btn btn-primary btn-block">Guardar</button>
    </div>
</div>

<script>
function addVideoField() {
    const wrapper = document.getElementById('videos-wrapper');
    const row = document.createElement('div');
    row.className = 'flex gap-2';
    row.innerHTML = `
        <input type="text" name="videos[]" placeholder="URL o ID de YouTube" class="input input-bordered w-full">
        <button type="button" class="btn btn-error btn-outline" onclick="this.parentElement.remove()">Quitar</button>
    `;
    wrapper.appendChild(row);
}

const generateEventAiBtn = document.getElementById('generate-ai-btn');
if (generateEventAiBtn) {
    generateEventAiBtn.addEventListener('click', async () => {
        const originalText = generateEventAiBtn.textContent;
        generateEventAiBtn.disabled = true;
        generateEventAiBtn.textContent = 'Generando...';

        const ciudad = document.getElementById('ai-ciudad-field')?.value || 'CDMX';
        const url = generateEventAiBtn.dataset.url + '?ciudad=' + encodeURIComponent(ciudad);

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json',
                },
            });

            const data = await response.json();

            if (!response.ok) {
                alert(data.message || 'Error al generar contenido.');
                return;
            }

            document.getElementById('summary-field').value = data.summary || '';
            document.getElementById('content-field').value = data.content || '';
            document.getElementById('meta-title-field').value = data.meta_title || '';
            document.getElementById('meta-description-field').value = data.meta_description || '';
            document.getElementById('meta-keywords-field').value = data.meta_keywords || '';

            replaceServiciosRows(data.servicios_especializados || []);
            replaceFaqsRows(data.preguntas_frecuentes || []);
        } catch (error) {
            alert('Error de conexión al generar contenido.');
        } finally {
            generateEventAiBtn.disabled = false;
            generateEventAiBtn.textContent = originalText;
        }
    });
}

function addServicioRow(titulo = '', descripcion = '') {
    const wrapper = document.getElementById('servicios-repeater');
    const row = document.createElement('div');
    row.className = 'grid grid-cols-1 sm:grid-cols-[1fr_2fr_auto] gap-2 items-start border border-base-300 rounded-box p-3';
    row.innerHTML = `
        <input type="text" name="servicio_titulo[]" value="${titulo.replace(/"/g, '&quot;')}" placeholder="Título del servicio" class="input input-bordered input-sm w-full">
        <textarea name="servicio_descripcion[]" rows="2" placeholder="Descripción" class="textarea textarea-bordered textarea-sm w-full">${descripcion}</textarea>
        <button type="button" class="btn btn-sm btn-error btn-outline" onclick="this.closest('.grid').remove()">✕</button>
    `;
    wrapper.appendChild(row);
}

function replaceServiciosRows(servicios) {
    const wrapper = document.getElementById('servicios-repeater');
    wrapper.innerHTML = '';
    servicios.forEach(s => addServicioRow(s.titulo_servicio || '', s.descripcion || ''));
}

document.getElementById('add-servicio')?.addEventListener('click', () => addServicioRow());

function addFaqRow(pregunta = '', respuesta = '') {
    const wrapper = document.getElementById('faqs-repeater');
    const row = document.createElement('div');
    row.className = 'grid grid-cols-1 sm:grid-cols-[1fr_2fr_auto] gap-2 items-start border border-base-300 rounded-box p-3';
    row.innerHTML = `
        <input type="text" name="faq_pregunta[]" value="${pregunta.replace(/"/g, '&quot;')}" placeholder="Pregunta" class="input input-bordered input-sm w-full">
        <textarea name="faq_respuesta[]" rows="2" placeholder="Respuesta" class="textarea textarea-bordered textarea-sm w-full">${respuesta}</textarea>
        <button type="button" class="btn btn-sm btn-error btn-outline" onclick="this.closest('.grid').remove()">✕</button>
    `;
    wrapper.appendChild(row);
}

function replaceFaqsRows(faqs) {
    const wrapper = document.getElementById('faqs-repeater');
    wrapper.innerHTML = '';
    faqs.forEach(f => addFaqRow(f.pregunta || '', f.respuesta || ''));
}

document.getElementById('add-faq')?.addEventListener('click', () => addFaqRow());
</script>


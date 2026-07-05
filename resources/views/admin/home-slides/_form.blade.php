@csrf

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Columna principal: contenido --}}
    <div class="lg:col-span-2 flex flex-col gap-4">

        <div class="card bg-base-100 border border-base-300">
            <div class="card-body gap-4">
                <h3 class="font-semibold">Contenido</h3>

                <div>
                    <label class="label"><span class="label-text">Título</span></label>
                    <input type="text" name="title" id="field-title" value="{{ old('title', $homeSlide->title ?? '') }}" class="input input-bordered w-full">
                    @error('title')
                        <p class="text-error text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="label"><span class="label-text">Caption</span></label>
                    <input type="text" name="caption" id="field-caption" value="{{ old('caption', $homeSlide->caption ?? '') }}" class="input input-bordered w-full">
                    @error('caption')
                        <p class="text-error text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="label"><span class="label-text">Resumen</span></label>
                    <textarea name="summary" id="field-summary" rows="3" class="textarea textarea-bordered w-full">{{ old('summary', $homeSlide->summary ?? '') }}</textarea>
                    @error('summary')
                        <p class="text-error text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="label"><span class="label-text">Texto del botón</span></label>
                        <input type="text" name="button_text" id="field-button-text" value="{{ old('button_text', $homeSlide->button_text ?? '') }}" class="input input-bordered w-full">
                        @error('button_text')
                            <p class="text-error text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="label"><span class="label-text">Enlace del botón</span></label>
                        <input type="text" name="button_url" value="{{ old('button_url', $homeSlide->button_url ?? '') }}" placeholder="https://... o /talento" class="input input-bordered w-full">
                        @error('button_url')
                            <p class="text-error text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Previsualización tipo Hero --}}
        <div class="card bg-base-100 border border-base-300">
            <div class="card-body">
                <h3 class="font-semibold mb-3">Previsualización</h3>

                <div id="hero-preview" class="relative w-full aspect-[16/9] rounded-box overflow-hidden bg-base-300"
                     style="background-image: url('{{ asset('images/fondo_hero.jpg') }}'); background-size: cover; background-position: center;">

                    <div class="absolute inset-0 bg-black/40"></div>

                    <div class="absolute inset-x-0 bottom-0 h-[85%] flex items-end justify-center pointer-events-none">
                        <img id="preview-image"
                             src="{{ isset($homeSlide) && $homeSlide->image ? Storage::url($homeSlide->image) : '' }}"
                             class="h-full w-auto max-w-full object-contain {{ isset($homeSlide) && $homeSlide->image ? '' : 'hidden' }}">
                    </div>

                    <div class="relative h-full flex items-end justify-center text-center pb-6 px-4">
                        <div class="max-w-md p-4 bg-black/50 rounded">
                            <span id="preview-caption" class="badge badge-primary mb-2 font-semibold {{ old('caption', $homeSlide->caption ?? '') ? '' : 'hidden' }}">
                                {{ old('caption', $homeSlide->caption ?? '') }}
                            </span>
                            <h1 id="preview-title" class="text-lg md:text-2xl font-bold text-white leading-tight uppercase">
                                {{ old('title', $homeSlide->title ?? 'Título del slide') }}
                            </h1>
                            <p id="preview-summary" class="py-2 text-xs md:text-sm text-white/90 {{ old('summary', $homeSlide->summary ?? '') ? '' : 'hidden' }}">
                                {{ old('summary', $homeSlide->summary ?? '') }}
                            </p>
                            <a id="preview-button" href="#" onclick="return false;" class="btn btn-outline btn-primary btn-sm rounded-none bg-transparent {{ old('button_text', $homeSlide->button_text ?? '') ? '' : 'hidden' }}">
                                {{ old('button_text', $homeSlide->button_text ?? '') }}
                            </a>
                        </div>
                    </div>
                </div>

                <p class="text-xs opacity-60 mt-2">Vista aproximada — el resultado real puede variar según el tamaño de pantalla.</p>
            </div>
        </div>

    </div>

    {{-- Columna lateral: imagen, orden, estado, guardar --}}
    <div class="lg:col-span-1">
        <div class="card bg-base-100 border border-base-300 lg:sticky lg:top-6">
            <div class="card-body gap-4">

                <div>
                    <label class="label"><span class="label-text">Imagen (PNG o WEBP transparente, 960x1050px)</span></label>
                    <input type="file" name="image" id="field-image" accept="image/png,image/webp" class="file-input file-input-bordered w-full">
                    @error('image')
                        <p class="text-error text-sm mt-1">{{ $message }}</p>
                    @enderror

                    @isset($homeSlide)
                        @if($homeSlide->image)
                            <img src="{{ Storage::url($homeSlide->image) }}" alt="{{ $homeSlide->title }}" class="mt-3 h-32 rounded-box bg-base-300 object-contain">
                        @endif
                    @endisset
                </div>

                <div>
                    <label class="label"><span class="label-text">Orden</span></label>
                    <input type="number" name="order" value="{{ old('order', $homeSlide->order ?? 0) }}" min="0" class="input input-bordered w-full">
                    @error('order')
                        <p class="text-error text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <label class="label cursor-pointer justify-start gap-2">
                    <input type="checkbox" name="is_active" value="1" class="checkbox"
                           {{ old('is_active', $homeSlide->is_active ?? true) ? 'checked' : '' }}>
                    <span class="label-text">Activo</span>
                </label>

                <div class="divider my-0"></div>

                <div class="flex flex-col gap-2">
                    <button type="submit" class="btn btn-primary w-full">Guardar</button>
                    <a href="{{ route('admin.home-slides.index') }}" class="btn btn-ghost w-full">Cancelar</a>
                </div>

            </div>
        </div>
    </div>

</div>

<script>
document.getElementById('field-title').addEventListener('input', (e) => {
    document.getElementById('preview-title').textContent = e.target.value || 'Título del slide';
});

document.getElementById('field-caption').addEventListener('input', (e) => {
    const el = document.getElementById('preview-caption');
    el.textContent = e.target.value;
    el.classList.toggle('hidden', !e.target.value);
});

document.getElementById('field-summary').addEventListener('input', (e) => {
    const el = document.getElementById('preview-summary');
    el.textContent = e.target.value;
    el.classList.toggle('hidden', !e.target.value);
});

document.getElementById('field-button-text').addEventListener('input', (e) => {
    const el = document.getElementById('preview-button');
    el.textContent = e.target.value;
    el.classList.toggle('hidden', !e.target.value);
});

document.getElementById('field-image').addEventListener('change', (e) => {
    const preview = document.getElementById('preview-image');
    const file = e.target.files[0];
    if (file) {
        preview.src = URL.createObjectURL(file);
        preview.classList.remove('hidden');
    }
});
</script>
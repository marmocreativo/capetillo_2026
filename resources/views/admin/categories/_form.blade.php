@if ($errors->any())
    <div class="alert alert-error mb-4">
        <ul class="list-disc list-inside text-sm">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Columna principal: contenido y meta --}}
    <div class="lg:col-span-2 flex flex-col gap-4">

        <div class="card bg-base-100 border border-base-300">
            <div class="card-body">
                <h3 class="font-semibold mb-2">Información general</h3>

                <div class="form-control">
                    <label class="label"><span class="label-text">Nombre</span></label>
                    <input type="text" name="name" value="{{ old('name', $category->name ?? '') }}" class="input input-bordered w-full" required>
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text">Slug (déjalo vacío para autogenerar)</span></label>
                    <input type="text" name="slug" value="{{ old('slug', $category->slug ?? '') }}" class="input input-bordered w-full">
                </div>
            </div>
        </div>

        <div class="card bg-base-100 border border-base-300">
            <div class="card-body">
                <h3 class="font-semibold mb-2">SEO</h3>

                <div class="form-control">
                    <label class="label"><span class="label-text">Meta título</span></label>
                    <input type="text" name="meta_title" value="{{ old('meta_title', $category->meta_title ?? '') }}" class="input input-bordered w-full">
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text">Meta descripción</span></label>
                    <textarea name="meta_description" class="textarea textarea-bordered w-full" rows="3">{{ old('meta_description', $category->meta_description ?? '') }}</textarea>
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text">Meta keywords</span></label>
                    <input type="text" name="meta_keywords" value="{{ old('meta_keywords', $category->meta_keywords ?? '') }}" class="input input-bordered w-full">
                </div>
            </div>
        </div>

    </div>

    {{-- Columna lateral: imagen, orden, estado, guardar --}}
    <div class="lg:col-span-1">
        <div class="card bg-base-100 border border-base-300 lg:sticky lg:top-6">
            <div class="card-body gap-4">

                <div class="form-control">
                    <label class="label"><span class="label-text">Imagen de portada</span></label>
                    <input type="file" name="cover_image" accept="image/*" class="file-input file-input-bordered w-full" onchange="previewCoverImage(event)">
                    <div class="mt-2">
                        <img id="cover-preview"
                             src="{{ isset($category) && $category->cover_image ? Storage::url($category->cover_image) : '' }}"
                             class="w-full h-40 object-cover rounded {{ isset($category) && $category->cover_image ? '' : 'hidden' }}">
                    </div>
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text">Orden</span></label>
                    <input type="number" name="order" value="{{ old('order', $category->order ?? 0) }}" class="input input-bordered w-full" min="0">
                </div>

                <label class="label cursor-pointer justify-start gap-2">
                    <input type="checkbox" name="is_active" value="1" class="checkbox" {{ old('is_active', $category->is_active ?? true) ? 'checked' : '' }}>
                    <span class="label-text">Categoría activa</span>
                </label>

                <div class="divider my-0"></div>

                <div class="flex flex-col gap-2">
                    <button type="submit" class="btn btn-primary w-full">Guardar</button>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-ghost w-full">Cancelar</a>
                </div>

            </div>
        </div>
    </div>

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
</script>
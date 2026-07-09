<div class="border border-base-300 rounded-box p-4 talent-entry-row">
    <div class="flex gap-4">
        <div class="w-20 h-20 shrink-0 rounded bg-base-200 overflow-hidden">
            @if ($entry->imagen)
                <img src="{{ Storage::url($entry->imagen) }}" class="w-full h-full object-cover">
            @endif
        </div>

        <form class="talent-update-form flex-1 grid grid-cols-1 sm:grid-cols-2 gap-3"
              action="{{ route('admin.contact-messages.talents.update', [$entry->contact_message_id, $entry]) }}">
            <div class="form-control">
                <label class="label"><span class="label-text text-xs">Nombre</span></label>
                <input type="text" name="nombre" value="{{ $entry->nombre }}" class="input input-bordered input-sm w-full">
            </div>
            <div class="form-control">
                <label class="label"><span class="label-text text-xs">Honorarios (MXN)</span></label>
                <input type="number" step="0.01" min="0" name="honorarios" value="{{ $entry->honorarios }}" class="input input-bordered input-sm w-full">
            </div>
            <div class="form-control sm:col-span-2">
                <label class="label"><span class="label-text text-xs">Incluye</span></label>
                <textarea name="incluye" rows="2" class="textarea textarea-bordered textarea-sm w-full">{{ $entry->incluye }}</textarea>
            </div>
            <div class="form-control sm:col-span-2">
                <label class="label"><span class="label-text text-xs">Condiciones de pago</span></label>
                <textarea name="condiciones_pago" rows="2" class="textarea textarea-bordered textarea-sm w-full">{{ $entry->condiciones_pago }}</textarea>
            </div>
            <div class="sm:col-span-2 flex justify-end gap-2">
                <button type="submit" class="btn btn-primary btn-xs">Guardar</button>
            </div>
        </form>

        <button type="button" class="btn btn-ghost btn-xs btn-square text-error talent-remove-btn"
                data-url="{{ route('admin.contact-messages.talents.destroy', [$entry->contact_message_id, $entry]) }}">
            ✕
        </button>
    </div>
</div>
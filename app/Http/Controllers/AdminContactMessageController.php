<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactMessageManualRequest;
use App\Http\Requests\StoreContactMessageTalentsRequest;
use App\Http\Requests\UpdateContactMessageRequest;
use App\Http\Requests\UpdateContactMessageTalentRequest;
use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\ContactMessageTalent;
use App\Models\Talent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Exports\ContactMessagesExport;
use Maatwebsite\Excel\Facades\Excel;

class AdminContactMessageController extends Controller
{
    public function __construct(protected \App\Services\ImageUploadService $imageUploadService)
    {
    }
    
    public function index(Request $request)
    {
        $query = ContactMessage::with('talent')->latest();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('talent_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $messages = $query->paginate(20)->withQueryString();

        return view('admin.contact-messages.index', compact('messages'));
    }

    public function export(Request $request)
    {
        $filters = $request->only(['search', 'type', 'status']);

        return Excel::download(
            new ContactMessagesExport($filters),
            'contactos-' . now()->format('Y-m-d_His') . '.xlsx'
        );
    }

    public function create()
    {
        $talents = Talent::with('categories')->orderBy('name')->get();

        return view('admin.contact-messages.create', compact('talents'));
    }

    public function store(StoreContactMessageManualRequest $request)
    {
        $data = $request->validated();
        $data['con_venta_boletos'] = $request->boolean('con_venta_boletos');
        $data['tiene_presupuesto'] = $request->has('tiene_presupuesto') ? $request->boolean('tiene_presupuesto') : null;
        $data['enviar_cotizacion'] = $request->boolean('enviar_cotizacion');

        if ($data['type'] === 'contratacion' && empty($data['talent_name'])) {
            $talent = Talent::find($data['talent_id']);
            $data['talent_name'] = $talent?->name;
        }

        $contactMessage = ContactMessage::create($data);

        return redirect()->route('admin.contact-messages.edit', $contactMessage)
            ->with('status', 'Cotización creada correctamente.');
    }

    public function edit(ContactMessage $contactMessage)
    {
        $contactMessage->load('cotizacionTalents.talent');
        $talents = Talent::with('categories')->orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        return view('admin.contact-messages.edit', compact('contactMessage', 'talents', 'categories'));
    }

    public function update(UpdateContactMessageRequest $request, ContactMessage $contactMessage)
    {
        $data = $request->validated();
        $data['tiene_presupuesto'] = $request->has('tiene_presupuesto') ? $request->boolean('tiene_presupuesto') : null;
        $data['enviar_cotizacion'] = $request->boolean('enviar_cotizacion');
        $data['con_venta_boletos'] = $request->boolean('con_venta_boletos');
        $data['datos_contacto'] = $this->buildContacto($request);

        $contactMessage->update(collect($data)->except(['contacto_tipo', 'contacto_valor'])->toArray());

        return redirect()->route('admin.contact-messages.edit', $contactMessage)
            ->with('status', 'Mensaje actualizado correctamente.');
    }

    protected function buildContacto(Request $request): array
    {
        $tipos = $request->input('contacto_tipo', []);
        $valores = $request->input('contacto_valor', []);

        $contacto = [];
        foreach ($tipos as $index => $tipo) {
            if (! empty($valores[$index])) {
                $contacto[] = ['tipo' => $tipo, 'valor' => $valores[$index]];
            }
        }

        return $contacto;
    }

    public function addTalents(StoreContactMessageTalentsRequest $request, ContactMessage $contactMessage)
    {
        $talents = Talent::whereIn('id', $request->validated('talent_ids'))->get();
        $existingIds = $contactMessage->cotizacionTalents()->pluck('talent_id')->toArray();
        $order = $contactMessage->cotizacionTalents()->max('orden') ?? 0;

        foreach ($talents as $talent) {
            if (in_array($talent->id, $existingIds, true)) {
                continue;
            }

            $imagePath = null;
            if ($talent->cover_image && Storage::disk('public')->exists($talent->cover_image)) {
                $extension = pathinfo($talent->cover_image, PATHINFO_EXTENSION) ?: 'webp';
                $imagePath = 'contact-messages/talents/' . Str::uuid() . '.' . $extension;
                Storage::disk('public')->copy($talent->cover_image, $imagePath);
            }

            $order++;

            ContactMessageTalent::create([
                'contact_message_id' => $contactMessage->id,
                'talent_id' => $talent->id,
                'nombre' => $talent->name,
                'imagen' => $imagePath,
                'honorarios' => $talent->honorarios_default,
                'orden' => $order,
            ]);
        }

        if ($request->wantsJson()) {
            $newEntries = $contactMessage->cotizacionTalents()->get();

            return response()->json([
                'message' => 'Talento(s) agregado(s) a la cotización.',
                'html' => $newEntries->map(fn ($entry) => view('admin.contact-messages.partials.talent-entry', ['entry' => $entry])->render())->implode(''),
            ]);
        }

        return redirect()->route('admin.contact-messages.edit', $contactMessage)
            ->with('status', 'Talento(s) agregado(s) a la cotización.');
    }

    public function updateTalent(UpdateContactMessageTalentRequest $request, ContactMessage $contactMessage, ContactMessageTalent $contactMessageTalent)
    {
        abort_unless($contactMessageTalent->contact_message_id === $contactMessage->id, 404);

        $contactMessageTalent->update($request->validated());

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Talento de la cotización actualizado.',
            ]);
        }

        return redirect()->route('admin.contact-messages.edit', $contactMessage)
            ->with('status', 'Talento de la cotización actualizado.');
    }

    public function removeTalent(Request $request, ContactMessage $contactMessage, ContactMessageTalent $contactMessageTalent)
    {
        abort_unless($contactMessageTalent->contact_message_id === $contactMessage->id, 404);

        if ($contactMessageTalent->imagen) {
            Storage::disk('public')->delete($contactMessageTalent->imagen);
        }

        $contactMessageTalent->delete();

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Talento removido de la cotización.']);
        }

        return redirect()->route('admin.contact-messages.edit', $contactMessage)
            ->with('status', 'Talento removido de la cotización.');
    }

    protected function formatTalentEntry(ContactMessageTalent $entry): array
    {
        return [
            'id' => $entry->id,
            'nombre' => $entry->nombre,
            'honorarios' => $entry->honorarios,
            'incluye' => $entry->incluye,
            'condiciones_pago' => $entry->condiciones_pago,
            'imagen_url' => $entry->imagen ? Storage::url($entry->imagen) : null,
        ];
    }

    public function destroy(ContactMessage $contactMessage)
    {
        $contactMessage->delete();

        return redirect()->route('admin.contact-messages.index')
            ->with('status', 'Mensaje eliminado correctamente.');
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return redirect()->route('admin.contact-messages.index')
                ->with('status', 'No seleccionaste ningún mensaje.');
        }

        $count = ContactMessage::whereIn('id', $ids)->count();
        ContactMessage::whereIn('id', $ids)->delete();

        return redirect()->route('admin.contact-messages.index')
            ->with('status', "{$count} mensaje(s) eliminado(s) correctamente.");
    }
}
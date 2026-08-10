<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateEventContactRequest;
use App\Models\EventContact;
use Illuminate\Http\Request;

class AdminEventContactController extends Controller
{
    public function index(Request $request)
    {
        $query = EventContact::with('event')->latest();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $contacts = $query->paginate(20)->withQueryString();

        return view('admin.event-contacts.index', compact('contacts'));
    }

    public function show(EventContact $eventContact)
    {
        $eventContact->load('event');

        return view('admin.event-contacts.show', compact('eventContact'));
    }

    public function update(UpdateEventContactRequest $request, EventContact $eventContact)
    {
        $eventContact->update($request->validated());

        return redirect()->route('admin.event-contacts.show', $eventContact)
            ->with('status', 'Estado actualizado correctamente.');
    }

    public function destroy(EventContact $eventContact)
    {
        $eventContact->delete();

        return redirect()->route('admin.event-contacts.index')->with('status', 'Contacto eliminado correctamente.');
    }
}
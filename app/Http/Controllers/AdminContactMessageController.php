<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateContactMessageRequest;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class AdminContactMessageController extends Controller
{
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

    public function edit(ContactMessage $contactMessage)
    {
        return view('admin.contact-messages.edit', compact('contactMessage'));
    }

    public function update(UpdateContactMessageRequest $request, ContactMessage $contactMessage)
    {
        $contactMessage->update($request->validated());

        return redirect()->route('admin.contact-messages.index')
            ->with('status', 'Mensaje actualizado correctamente.');
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
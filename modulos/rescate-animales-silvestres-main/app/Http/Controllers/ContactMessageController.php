<?php

namespace Modules\Rescate\Http\Controllers;

use Modules\Rescate\Http\Requests\ContactMessageRequest;
use Modules\Rescate\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ContactMessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(ContactMessageRequest $request): RedirectResponse
    {
        ContactMessage::create([
            'user_id' => Auth::id(),
            'motivo' => $request->input('motivo'),
            'mensaje' => $request->input('mensaje'),
        ]);

        return redirect()->route('rescate.profile.index')
            ->with('success', 'Tu mensaje ha sido enviado. Un administrador o encargado se pondrá en contacto contigo pronto.');
    }

    public function update(Request $request, ContactMessage $contactMessage): RedirectResponse
    {
        $contactMessage->update([
            'leido' => true,
            'leido_at' => now(),
            'leido_por' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Mensaje marcado como leído.');
    }
}

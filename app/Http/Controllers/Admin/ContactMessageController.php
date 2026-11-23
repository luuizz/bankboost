<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ContactMessageController extends Controller
{
    public function index(Request $request): Response
    {
        $unread = $request->boolean('nao_lidas');

        return Inertia::render('admin/Messages', [
            'messages' => ContactMessage::query()
                ->when($unread, fn ($query) => $query->whereNull('read_at'))
                ->latest()
                ->paginate(20)
                ->withQueryString()
                ->through(fn (ContactMessage $message) => [
                    ...$message->only(['id', 'name', 'email', 'message']),
                    'subject' => $message->subject->label(),
                    'read' => $message->isRead(),
                    'created_at' => $message->created_at?->toIso8601String(),
                ]),
            'unreadOnly' => $unread,
        ]);
    }

    public function read(ContactMessage $message): RedirectResponse
    {
        $message->forceFill(['read_at' => $message->read_at ?? now()])->save();

        return back();
    }

    public function destroy(ContactMessage $message): RedirectResponse
    {
        $message->delete();

        return back()->with('success', 'Mensagem excluída.');
    }
}

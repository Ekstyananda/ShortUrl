<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactMessageController extends Controller
{
    public function index(Request $request): View
    {
        $filter = $request->query('filter') === 'unread' ? 'unread' : 'all';

        $messages = ContactMessage::query()
            ->when($filter === 'unread', fn ($q) => $q->unread())
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.messages.index', compact('messages', 'filter'));
    }

    public function show(ContactMessage $message): View
    {
        if (! $message->isRead()) {
            $message->forceFill(['read_at' => now()])->save();
        }

        return view('admin.messages.show', compact('message'));
    }

    public function markUnread(ContactMessage $message): RedirectResponse
    {
        $message->forceFill(['read_at' => null])->save();

        return redirect()->route('admin.messages.index')->with('status', 'Pesan ditandai belum dibaca.');
    }

    public function destroy(ContactMessage $message): RedirectResponse
    {
        AuditLogger::log('message.deleted', $message, ['from' => $message->email]);
        $message->delete();

        return redirect()->route('admin.messages.index')->with('status', 'Pesan dihapus.');
    }
}

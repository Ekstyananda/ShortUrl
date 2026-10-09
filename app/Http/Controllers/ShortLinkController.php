<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShortLinkRequest;
use App\Models\ShortLink;
use App\Services\AliasGenerator;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ShortLinkController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $search = trim((string) $request->query('q'));

        $links = ShortLink::query()
            ->visibleTo($user)
            ->with('user:id,name')
            ->withCount('clickEvents')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('alias', 'like', "%{$search}%")
                        ->orWhere('title', 'like', "%{$search}%")
                        ->orWhere('destination_url', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('links.index', compact('links', 'search'));
    }

    public function create(): View
    {
        return view('links.create', ['link' => new ShortLink]);
    }

    public function store(ShortLinkRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['alias'] ??= AliasGenerator::generate();

        $link = $request->user()->shortLinks()->create($data);

        AuditLogger::log('link.created', $link, [
            'alias' => $link->alias,
            'destination_host' => $link->destinationHost(),
        ]);

        return redirect()->route('links.show', $link)->with('status', 'Short URL berhasil dibuat.');
    }

    public function show(ShortLink $link): View
    {
        Gate::authorize('view', $link);

        $link->loadCount('clickEvents')->load('user:id,name,email');

        return view('links.show', compact('link'));
    }

    public function edit(ShortLink $link): View
    {
        Gate::authorize('update', $link);

        return view('links.edit', compact('link'));
    }

    public function update(ShortLinkRequest $request, ShortLink $link): RedirectResponse
    {
        Gate::authorize('update', $link);

        $data = $request->validated();
        $data['alias'] ??= $link->alias;

        $link->fill($data);
        $changes = array_keys($link->getDirty());
        $original = $link->getOriginal();
        $link->save();

        if ($changes) {
            $metadata = ['changed' => $changes];
            if (in_array('destination_url', $changes, true)) {
                $metadata['old_destination_host'] = parse_url($original['destination_url'], PHP_URL_HOST);
                $metadata['new_destination_host'] = $link->destinationHost();
            }
            if (in_array('alias', $changes, true)) {
                $metadata['old_alias'] = $original['alias'];
                $metadata['new_alias'] = $link->alias;
            }
            AuditLogger::log('link.updated', $link, $metadata);
        }

        return redirect()->route('links.show', $link)->with('status', 'Short URL berhasil diperbarui.');
    }

    public function toggle(ShortLink $link): RedirectResponse
    {
        Gate::authorize('update', $link);

        $link->update(['is_active' => ! $link->is_active]);

        AuditLogger::log($link->is_active ? 'link.activated' : 'link.deactivated', $link, ['alias' => $link->alias]);

        return back()->with('status', $link->is_active ? 'Link diaktifkan.' : 'Link dinonaktifkan.');
    }

    public function destroy(ShortLink $link): RedirectResponse
    {
        Gate::authorize('delete', $link);

        AuditLogger::log('link.deleted', $link, [
            'alias' => $link->alias,
            'destination_host' => $link->destinationHost(),
            'owner_id' => $link->user_id,
        ]);

        $link->delete();

        return redirect()->route('links.index')->with('status', 'Short URL dihapus.');
    }
}

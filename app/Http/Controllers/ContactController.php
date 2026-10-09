<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        // Honeypot: bot biasanya mengisi semua field, termasuk yang tersembunyi.
        // Pura-pura berhasil agar bot tidak mencoba cara lain.
        if (filled($request->input('website'))) {
            return redirect(url('/').'#kontak')->with('contact_status', 'Terima kasih! Pesan Anda sudah kami terima.');
        }

        $data = $request->validateWithBag('contact', [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30', 'regex:/^[0-9+()\-\s]{6,30}$/'],
            'organization' => ['nullable', 'string', 'max:150'],
            'message' => ['required', 'string', 'min:10', 'max:2000'],
        ], [
            'phone.regex' => 'Nomor WhatsApp/telepon tidak valid.',
        ], [
            'name' => 'nama',
            'phone' => 'nomor WhatsApp',
            'organization' => 'instansi/usaha',
            'message' => 'pesan',
        ]);

        ContactMessage::create($data);

        return redirect(url('/').'#kontak')->with('contact_status', 'Terima kasih! Pesan Anda sudah kami terima, kami akan segera menghubungi Anda.');
    }
}

<?php

namespace App\Http\Requests;

use App\Models\ShortLink;
use App\Rules\AllowedAlias;
use App\Rules\SafeUrl;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ShortLinkRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Otorisasi kepemilikan dilakukan oleh ShortLinkPolicy di controller.
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'destination_url' => trim((string) $this->input('destination_url')),
            'alias' => strtolower(trim((string) $this->input('alias'))) ?: null,
            'title' => trim((string) $this->input('title')) ?: null,
            'expires_at' => $this->input('expires_at') ?: null,
        ]);
    }

    public function rules(): array
    {
        /** @var ShortLink|null $link */
        $link = $this->route('link');

        return [
            'destination_url' => ['required', 'string', 'max:2048', new SafeUrl],
            'alias' => [
                'nullable',
                'string',
                'min:2',
                'max:64',
                'regex:/^[A-Za-z0-9][A-Za-z0-9_-]*$/',
                new AllowedAlias,
                Rule::unique('short_links', 'alias')->ignore($link?->id),
            ],
            'title' => ['nullable', 'string', 'max:255'],
            'expires_at' => ['nullable', 'date', 'after:now'],
        ];
    }

    public function messages(): array
    {
        return [
            'alias.regex' => 'Alias hanya boleh berisi huruf, angka, tanda hubung (-) dan garis bawah (_), diawali huruf atau angka.',
            'alias.unique' => 'Alias ini sudah dipakai.',
            'expires_at.after' => 'Tanggal kedaluwarsa harus di masa depan.',
        ];
    }

    public function attributes(): array
    {
        return [
            'destination_url' => 'URL tujuan',
            'alias' => 'alias',
            'title' => 'judul',
            'expires_at' => 'tanggal kedaluwarsa',
        ];
    }
}

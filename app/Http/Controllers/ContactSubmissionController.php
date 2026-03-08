<?php

namespace App\Http\Controllers;

use App\Models\ContactSubmission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ContactSubmissionController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $payload = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:190'],
            'company' => ['nullable', 'string', 'max:160'],
            'summary' => ['required', 'string', 'min:20', 'max:4000'],
        ]);

        ContactSubmission::query()->create([
            'locale' => app()->getLocale(),
            'name' => $payload['name'],
            'email' => $payload['email'],
            'company' => $payload['company'] ?: null,
            'summary' => $payload['summary'],
            'status' => 'new',
        ]);

        return to_route('contact')->with(
            'status',
            app()->getLocale() === 'fr'
                ? 'Message enregistre. Je pourrai le lire depuis le back-office.'
                : 'Message saved. I can now review it from the back office.',
        );
    }
}

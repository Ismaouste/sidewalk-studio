<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class ContactSubmissionController extends Controller
{
    public function store(Request $request): Response|RedirectResponse
    {
        $payload = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:190'],
            'company' => ['nullable', 'string', 'max:160'],
            'summary' => ['required', 'string', 'min:20', 'max:4000'],
        ]);

        $prefix = app()->getLocale() === 'fr'
            ? "Bonjour Ismaël,%0A%0AVoici un premier message depuis le site :%0A"
            : "Hello Ismael,%0A%0AHere is a first message from the website:%0A";

        $lines = array_filter([
            'Name: '.$payload['name'],
            'Email: '.$payload['email'],
            $payload['company'] ? 'Company: '.$payload['company'] : null,
            '',
            $payload['summary'],
        ]);

        return Inertia::location(
            'https://wa.me/33684907698?text='.$prefix.rawurlencode(implode("\n", $lines))
        );
    }
}

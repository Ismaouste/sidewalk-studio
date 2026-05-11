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

        $isFrench = app()->getLocale() === 'fr';
        $subject = ($isFrench ? 'Prise de contact' : 'Inquiry')
            .($payload['company'] ? ': '.$payload['company'] : '');

        $lines = array_filter([
            ($isFrench ? 'Nom : ' : 'Name: ').$payload['name'],
            ($isFrench ? 'Email : ' : 'Email: ').$payload['email'],
            $payload['company']
                ? ($isFrench ? 'Entreprise ou produit : ' : 'Company or product: ').$payload['company']
                : null,
            '',
            $payload['summary'],
        ]);

        $email = (string) config('site.contact.email', 'ismael@rodmacq.com');

        return Inertia::location(
            'mailto:'.$email
            .'?subject='.rawurlencode($subject)
            .'&body='.rawurlencode(implode("\n", $lines))
        );
    }
}

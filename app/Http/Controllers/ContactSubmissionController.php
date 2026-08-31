<?php

namespace App\Http\Controllers;

use App\Support\PublicCopy;
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

        /**
         * The visitor reads this in their own mail client before sending it,
         * so it is copy like any other — and it was the last pair of
         * locale-branched strings left in a controller.
         */
        $copy = PublicCopy::group('contact_mail');

        $subject = $copy['subject']
            .($payload['company'] ? ': '.$payload['company'] : '');

        $lines = array_filter([
            $copy['name'].$payload['name'],
            $copy['email'].$payload['email'],
            $payload['company'] ? $copy['company'].$payload['company'] : null,
            '',
            $payload['summary'],
        ]);

        $email = (string) config('site.contact.email');

        return Inertia::location(
            'mailto:'.$email
            .'?subject='.rawurlencode($subject)
            .'&body='.rawurlencode(implode("\n", $lines))
        );
    }
}

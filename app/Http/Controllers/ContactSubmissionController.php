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
            'project_type' => ['nullable', 'string', 'max:120'],
            'budget' => ['nullable', 'string', 'max:120'],
            'timeline' => ['nullable', 'string', 'max:120'],
        ]);

        /**
         * `validate()` returns only the keys the request carried, so an
         * absent optional field is a missing key, not a null — reading it
         * bare was a 500 waiting for the first client that omits the input.
         */
        $payload += [
            'company' => null,
            'project_type' => null,
            'budget' => null,
            'timeline' => null,
        ];

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
            $payload['project_type'] ? $copy['project_type'].$payload['project_type'] : null,
            $payload['budget'] ? $copy['budget'].$payload['budget'] : null,
            $payload['timeline'] ? $copy['timeline'].$payload['timeline'] : null,
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

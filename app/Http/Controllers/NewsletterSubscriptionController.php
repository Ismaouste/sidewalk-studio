<?php

namespace App\Http\Controllers;

use App\Newsletter\NewsletterDeliveryException;
use App\Newsletter\NewsletterDriver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NewsletterSubscriptionController extends Controller
{
    public function __invoke(Request $request, NewsletterDriver $newsletter): JsonResponse
    {
        $payload = $request->validate([
            'email' => ['required', 'email:rfc', 'max:190'],
            'segment' => ['required', 'in:engineering,local-business'],
            'locale' => ['required', 'in:en,fr'],
            'company_website' => ['nullable', 'string', 'max:200'],
        ]);

        // The honeypot answers exactly like a success so a bot learns
        // nothing from the response shape.
        if (($payload['company_website'] ?? '') === '') {
            try {
                $newsletter->subscribe($payload['email'], $payload['segment'], $payload['locale']);
            } catch (NewsletterDeliveryException) {
                return response()->json(['status' => 'error'], 503);
            }
        }

        return response()->json(['status' => 'pending_confirmation'], 202);
    }
}

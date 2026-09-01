<?php

namespace App\Http\Controllers;

use App\Audit\AuditReport;
use App\Audit\AuditUnavailableException;
use App\Audit\PageSpeedClient;
use App\Mail\AuditReportMail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AuditRequestController extends Controller
{
    public function __invoke(Request $request, PageSpeedClient $pageSpeed): JsonResponse
    {
        $payload = $request->validate([
            'url' => ['required', 'url:http,https', 'max:300'],
            'email' => ['required', 'email:rfc', 'max:190'],
            'locale' => ['required', 'in:en,fr'],
            'company_website' => ['nullable', 'string', 'max:200'],
        ]);

        if (! config('audit.enabled')) {
            return response()->json(['status' => 'unavailable'], 503);
        }

        // Honeypot: answer like a success, do no work.
        if (($payload['company_website'] ?? '') !== '') {
            return response()->json(['status' => 'sent', 'report' => null]);
        }

        try {
            $report = AuditReport::fromPageSpeed(
                $pageSpeed->run($payload['url'], $payload['locale']),
            );
        } catch (AuditUnavailableException) {
            return response()->json(['status' => 'unavailable'], 502);
        }

        Mail::to($payload['email'])->send(new AuditReportMail($report, $payload['locale']));

        return response()->json(['status' => 'sent', 'report' => $report]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Audience\AudienceSink;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

/**
 * Consent tier T1: a first-party, cookieless page-count ping.
 *
 * Designed against the CNIL audience-measurement exemption criteria: the
 * route carries no session and sets no cookie, the client IP is truncated
 * before use and only survives inside an HMAC digest whose salt rotates
 * daily, the referrer is reduced to its host, and Global Privacy Control
 * is honored server-side. The digest exists to count unique visitors
 * within one day; by the next day it cannot be recomputed.
 */
final class AudiencePingController
{
    public function __invoke(Request $request, AudienceSink $sink): Response|JsonResponse
    {
        if (! config('audience.enabled') || $request->headers->get('Sec-GPC') === '1') {
            return response()->noContent();
        }

        // Manual validation: this route has no session, so the exception
        // handler's redirect-with-errors path must never be reachable.
        $validator = Validator::make($request->all(), [
            'path' => ['required', 'string', 'starts_with:/', 'max:200'],
            'locale' => ['required', 'in:en,fr'],
            'referrer' => ['nullable', 'url:http,https', 'max:300'],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid ping.'], 422);
        }

        $sink->record($this->event($request, $validator->validated()));

        return response()->noContent();
    }

    /**
     * @param  array{path: string, locale: string, referrer?: string|null}  $input
     * @return array<string, string|null>
     */
    private function event(Request $request, array $input): array
    {
        $date = now()->toDateString();
        $userAgent = (string) $request->userAgent();

        return [
            'date' => $date,
            'path' => $this->normalizePath($input['path']),
            'locale' => $input['locale'],
            'referrer_host' => $this->referrerHost($input['referrer'] ?? null),
            'device' => str_contains($userAgent, 'Mobi') ? 'mobile' : 'desktop',
            'visitor' => $this->visitorDigest($request->ip(), $userAgent, $date),
            'occurred_at' => now()->toIso8601String(),
        ];
    }

    private function normalizePath(string $path): string
    {
        $stripped = (string) preg_replace('#^/(en|fr)(?=/|$)#', '', $path);

        return $stripped === '' ? '/' : $stripped;
    }

    private function referrerHost(?string $referrer): ?string
    {
        if ($referrer === null) {
            return null;
        }

        $host = parse_url($referrer, PHP_URL_HOST);
        $ownHost = parse_url((string) config('site.url'), PHP_URL_HOST);

        return is_string($host) && $host !== $ownHost ? $host : null;
    }

    /**
     * Sixteen hex characters that mean "probably the same visitor, today".
     *
     * The IP is truncated first (IPv4 to /24, IPv6 to /48) so the full
     * address never enters the computation, and the salt includes the date
     * so yesterday's digests cannot be joined with today's.
     */
    private function visitorDigest(?string $ip, string $userAgent, string $date): string
    {
        return substr(hash_hmac(
            'sha256',
            $this->truncateIp($ip).'|'.$userAgent,
            config('app.key').'|'.$date,
        ), 0, 16);
    }

    private function truncateIp(?string $ip): string
    {
        if ($ip === null) {
            return 'unknown';
        }

        if (str_contains($ip, ':')) {
            return implode(':', array_slice(explode(':', $ip), 0, 3)).'::';
        }

        $octets = explode('.', $ip);
        $octets[count($octets) - 1] = '0';

        return implode('.', $octets);
    }
}

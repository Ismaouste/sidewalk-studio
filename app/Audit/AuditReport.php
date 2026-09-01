<?php

namespace App\Audit;

final class AuditReport
{
    /**
     * Normalize the PSI v5 payload into the mini-report shared by the
     * mailable and the Labs page. Every leaf tolerates absence: a brand-new
     * site has no CrUX field data, and a broken Lighthouse run may miss a
     * category.
     *
     * @param  array<string, mixed>  $json
     * @return array<string, mixed>
     */
    public static function fromPageSpeed(array $json): array
    {
        $lighthouse = (array) ($json['lighthouseResult'] ?? []);
        $categories = (array) ($lighthouse['categories'] ?? []);
        $audits = (array) ($lighthouse['audits'] ?? []);
        $field = (array) (data_get($json, 'loadingExperience.metrics') ?? []);

        $opportunities = [];

        foreach ($audits as $audit) {
            $details = (array) ($audit['details'] ?? []);
            $score = $audit['score'] ?? null;

            if (($details['type'] ?? null) !== 'opportunity' || ! is_numeric($score) || $score >= 0.9) {
                continue;
            }

            $opportunities[] = [
                'title' => (string) ($audit['title'] ?? ''),
                'savings' => (string) ($audit['displayValue'] ?? ''),
            ];
        }

        return [
            'url' => (string) ($lighthouse['finalDisplayedUrl'] ?? $json['id'] ?? ''),
            'scores' => [
                'performance' => self::score($categories, 'performance'),
                'seo' => self::score($categories, 'seo'),
            ],
            'field' => [
                'lcp' => self::fieldMetric($field, 'LARGEST_CONTENTFUL_PAINT_MS'),
                'inp' => self::fieldMetric($field, 'INTERACTION_TO_NEXT_PAINT'),
                'cls' => self::fieldMetric($field, 'CUMULATIVE_LAYOUT_SHIFT_SCORE'),
            ],
            'lab' => [
                'lcp' => self::displayValue($audits, 'largest-contentful-paint'),
                'cls' => self::displayValue($audits, 'cumulative-layout-shift'),
                'tbt' => self::displayValue($audits, 'total-blocking-time'),
            ],
            'opportunities' => array_slice($opportunities, 0, 5),
        ];
    }

    /**
     * @param  array<string, mixed>  $categories
     */
    private static function score(array $categories, string $key): ?int
    {
        $score = data_get($categories, "{$key}.score");

        return is_numeric($score) ? (int) round($score * 100) : null;
    }

    /**
     * @param  array<string, mixed>  $metrics
     * @return array{percentile: int, category: string}|null
     */
    private static function fieldMetric(array $metrics, string $key): ?array
    {
        $metric = $metrics[$key] ?? null;

        if (! is_array($metric) || ! isset($metric['percentile'], $metric['category'])) {
            return null;
        }

        return [
            'percentile' => (int) $metric['percentile'],
            'category' => (string) $metric['category'],
        ];
    }

    /**
     * @param  array<string, mixed>  $audits
     */
    private static function displayValue(array $audits, string $key): ?string
    {
        $value = data_get($audits, "{$key}.displayValue");

        return is_string($value) && $value !== '' ? $value : null;
    }
}

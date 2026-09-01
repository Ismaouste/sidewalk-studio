<?php

namespace Tests\Feature;

use App\Audit\AuditReport;
use App\Mail\AuditReportMail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AuditRequestTest extends TestCase
{
    /**
     * @return array<string, mixed>
     */
    private function pageSpeedResponse(): array
    {
        return [
            'id' => 'https://example.com/',
            'loadingExperience' => [
                'metrics' => [
                    'LARGEST_CONTENTFUL_PAINT_MS' => ['percentile' => 2400, 'category' => 'AVERAGE'],
                    'INTERACTION_TO_NEXT_PAINT' => ['percentile' => 180, 'category' => 'FAST'],
                    'CUMULATIVE_LAYOUT_SHIFT_SCORE' => ['percentile' => 5, 'category' => 'FAST'],
                ],
            ],
            'lighthouseResult' => [
                'finalDisplayedUrl' => 'https://example.com/',
                'categories' => [
                    'performance' => ['score' => 0.62],
                    'seo' => ['score' => 0.85],
                ],
                'audits' => [
                    'largest-contentful-paint' => ['displayValue' => '2.4 s'],
                    'cumulative-layout-shift' => ['displayValue' => '0.05'],
                    'total-blocking-time' => ['displayValue' => '150 ms'],
                    'render-blocking-resources' => [
                        'title' => 'Eliminate render-blocking resources',
                        'score' => 0.5,
                        'displayValue' => 'Potential savings of 300 ms',
                        'details' => ['type' => 'opportunity'],
                    ],
                    'uses-optimized-images' => [
                        'title' => 'Efficiently encode images',
                        'score' => 1,
                        'displayValue' => '',
                        'details' => ['type' => 'opportunity'],
                    ],
                ],
            ],
        ];
    }

    public function test_an_audit_request_mails_the_report_and_returns_the_summary(): void
    {
        Mail::fake();
        Http::fake([
            'www.googleapis.com/*' => Http::response($this->pageSpeedResponse()),
        ]);

        $response = $this->postJson('/labs/audit', [
            'url' => 'https://example.com',
            'email' => 'merchant@example.com',
            'locale' => 'fr',
        ]);

        $response->assertOk();
        $response->assertJsonPath('status', 'sent');
        $response->assertJsonPath('report.scores.performance', 62);
        $response->assertJsonPath('report.scores.seo', 85);
        $response->assertJsonPath('report.field.lcp.category', 'AVERAGE');
        $this->assertNull($response->headers->get('Set-Cookie'));

        Mail::assertSent(AuditReportMail::class, function (AuditReportMail $mail): bool {
            return $mail->hasTo('merchant@example.com') && $mail->reportLocale === 'fr';
        });
    }

    public function test_scored_out_opportunities_are_dropped_from_the_report(): void
    {
        $report = AuditReport::fromPageSpeed($this->pageSpeedResponse());

        $this->assertCount(1, $report['opportunities']);
        $this->assertSame('Eliminate render-blocking resources', $report['opportunities'][0]['title']);
    }

    public function test_a_page_speed_failure_surfaces_without_sending_mail(): void
    {
        Mail::fake();
        Http::fake(['www.googleapis.com/*' => Http::response(null, 500)]);

        $this->postJson('/labs/audit', [
            'url' => 'https://example.com',
            'email' => 'merchant@example.com',
            'locale' => 'en',
        ])->assertStatus(502)->assertJson(['status' => 'unavailable']);

        Mail::assertNothingSent();
    }

    public function test_invalid_payloads_are_rejected_before_any_outbound_call(): void
    {
        Http::fake();
        Mail::fake();

        $this->postJson('/labs/audit', [
            'url' => 'ftp://example.com',
            'email' => 'merchant@example.com',
            'locale' => 'en',
        ])->assertStatus(422);

        $this->postJson('/labs/audit', [
            'url' => 'https://example.com',
            'email' => 'nope',
            'locale' => 'en',
        ])->assertStatus(422);

        Http::assertNothingSent();
    }

    public function test_a_filled_honeypot_never_reaches_page_speed(): void
    {
        Http::fake();
        Mail::fake();

        $this->postJson('/labs/audit', [
            'url' => 'https://example.com',
            'email' => 'bot@example.com',
            'locale' => 'en',
            'company_website' => 'https://spam.example',
        ])->assertOk()->assertJsonPath('report', null);

        Http::assertNothingSent();
        Mail::assertNothingSent();
    }

    public function test_the_audit_page_renders_in_both_locales(): void
    {
        $this->get('/en/labs/audit')->assertOk();

        $french = $this->get('/fr/labs/audit');
        $french->assertOk();
        $french->assertSee('lang="fr"', false);
    }

    public function test_the_report_mail_renders_in_both_locales(): void
    {
        $report = AuditReport::fromPageSpeed($this->pageSpeedResponse());

        $english = (new AuditReportMail($report, 'en'))->render();
        $french = (new AuditReportMail($report, 'fr'))->render();

        $this->assertStringContainsString('62', $english);
        $this->assertStringContainsString('Core Web Vitals', $english);
        $this->assertStringContainsString('Core Web Vitals', $french);
        $this->assertNotSame($english, $french);
    }
}

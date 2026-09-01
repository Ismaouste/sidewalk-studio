<?php

namespace App\Mail;

use App\Support\PublicCopy;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class AuditReportMail extends Mailable
{
    /**
     * The base Mailable already owns an untyped `$locale`, so the report's
     * locale travels under its own name.
     *
     * @param  array<string, mixed>  $report
     */
    public function __construct(
        public array $report,
        public string $reportLocale,
    ) {}

    public function envelope(): Envelope
    {
        $copy = PublicCopy::group('audit_mail', $this->reportLocale);
        $host = (string) parse_url((string) $this->report['url'], PHP_URL_HOST);

        return new Envelope(subject: $copy['subject'].' — '.$host);
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.audit-report',
            with: [
                'report' => $this->report,
                'copy' => PublicCopy::group('audit_mail', $this->reportLocale),
                'labSummary' => $this->labSummary(),
                'servicesUrl' => rtrim((string) config('site.url'), '/')."/{$this->reportLocale}/services",
            ],
        );
    }

    private function labSummary(): ?string
    {
        $lab = $this->report['lab'];

        $parts = array_filter([
            $lab['lcp'] !== null ? 'LCP '.$lab['lcp'] : null,
            $lab['tbt'] !== null ? 'TBT '.$lab['tbt'] : null,
            $lab['cls'] !== null ? 'CLS '.$lab['cls'] : null,
        ]);

        return $parts === [] ? null : implode(' · ', $parts);
    }
}

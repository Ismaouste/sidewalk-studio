<x-mail::message>
# {{ $copy['heading'] }}

{{ $copy['intro'] }} **{{ $report['url'] }}**

## {{ $copy['scores_heading'] }}

- {{ $copy['performance_label'] }}: **{{ $report['scores']['performance'] ?? '—' }}/100**
- {{ $copy['seo_label'] }}: **{{ $report['scores']['seo'] ?? '—' }}/100**

## Core Web Vitals

@if ($report['field']['lcp'] || $report['field']['inp'] || $report['field']['cls'])
{{ $copy['field_note'] }}

<x-mail::table>
| {{ $copy['metric_label'] }} | {{ $copy['value_label'] }} | {{ $copy['rating_label'] }} |
| :-- | :-- | :-- |
@if ($report['field']['lcp'])
| LCP | {{ number_format($report['field']['lcp']['percentile'] / 1000, 1) }} s | {{ $copy['ratings'][$report['field']['lcp']['category']] ?? $report['field']['lcp']['category'] }} |
@endif
@if ($report['field']['inp'])
| INP | {{ $report['field']['inp']['percentile'] }} ms | {{ $copy['ratings'][$report['field']['inp']['category']] ?? $report['field']['inp']['category'] }} |
@endif
@if ($report['field']['cls'])
| CLS | {{ number_format($report['field']['cls']['percentile'] / 100, 2) }} | {{ $copy['ratings'][$report['field']['cls']['category']] ?? $report['field']['cls']['category'] }} |
@endif
</x-mail::table>
@else
{{ $copy['no_field_data'] }}
@endif

@if ($labSummary !== null)
{{ $copy['lab_note'] }} {{ $labSummary }}
@endif

@if (count($report['opportunities']) > 0)
## {{ $copy['opportunities_heading'] }}

@foreach ($report['opportunities'] as $opportunity)
- **{{ $opportunity['title'] }}**@if ($opportunity['savings'] !== '') — {{ $opportunity['savings'] }}@endif
@endforeach
@endif

{{ $copy['outro'] }}

<x-mail::button :url="$servicesUrl">
{{ $copy['cta'] }}
</x-mail::button>

{{ $copy['signature'] }}
</x-mail::message>

<?php

namespace App\Services;

use App\Models\ExperienceEntry;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * The experience record, read as rows rather than as three arrays.
 *
 * The page payload keeps the shape it always had, and this service is what
 * produces it. That is deliberate: the public components, their props and
 * their tests are untouched by the move, so the only thing that changes on
 * the day this lands is where the sections come from.
 *
 * Every read is guarded by `Schema::hasTable`. Vercel ships no SQLite, so on
 * production this returns nothing and the caller falls back to the payload —
 * the same contract every other repository here honours.
 */
class ExperienceEntryRepository
{
    /** How a role and its dates are joined into the eyebrow the page reads. */
    public const EYEBROW_SEPARATOR = ' — ';

    public function hasEntries(string $locale): bool
    {
        if (! Schema::hasTable('experience_entries')) {
            return false;
        }

        return ExperienceEntry::query()->where('locale', $locale)->exists();
    }

    /**
     * The three families, each already in chronological order, shaped exactly
     * as the page payload shaped them.
     *
     * @return array<string, array<int, array<string, mixed>>>
     */
    public function sectionsFor(string $locale): array
    {
        $sections = array_fill_keys(ExperienceEntry::KINDS, []);

        if (! Schema::hasTable('experience_entries')) {
            return $sections;
        }

        $rows = ExperienceEntry::query()
            ->where('locale', $locale)
            ->inChronologicalOrder()
            ->get();

        foreach ($rows as $row) {
            $sections[$row->kind][] = $this->asSection($row);
        }

        return $sections;
    }

    /**
     * @return array<string, mixed>
     */
    public function asSection(ExperienceEntry $entry): array
    {
        return [
            'title' => $entry->organisation,
            'eyebrow' => $entry->role.self::EYEBROW_SEPARATOR.$this->dateRangeFor($entry),
            'summary' => $entry->summary,
            'paragraphs' => $entry->paragraphs ?? [],
            'detail_groups' => $entry->detail_groups ?? [],
        ];
    }

    /**
     * What the dates say, or what the operator says instead.
     *
     * A label wins when it is set, because some of this content is honestly
     * imprecise and `Avant 2023` is not a date range with a missing half. When
     * no label is set the years are computed, and an entry with no end is
     * current — which is the whole point of storing the dates: the page stops
     * needing an edit every January.
     */
    public function dateRangeFor(ExperienceEntry $entry): string
    {
        if (filled($entry->date_label)) {
            return (string) $entry->date_label;
        }

        $start = $entry->started_on?->format('Y');

        if ($start === null) {
            return '';
        }

        $end = $entry->ended_on?->format('Y');

        if ($end === null) {
            return (string) __('public.experience.since', ['year' => $start]);
        }

        return $end === $start ? $start : "{$start}–{$end}";
    }

    /**
     * Turn the three arrays in the `experience` payload into rows, once.
     *
     * Seeding is not a translation of the content, it is a re-filing of it:
     * every string that reached a reader before still reaches them, byte for
     * byte. The eyebrow is split into a role and a date label rather than
     * parsed into dates, because `Avant 2023` has no correct date and guessing
     * one would put an invented fact in a CV. Dates are what the operator
     * fills in afterwards, one row at a time, and clearing the label is what
     * hands the display over to them.
     *
     * @param  array<string, array<string, mixed>>  $payloadsByLocale
     */
    public function seedFromPayloads(array $payloadsByLocale): int
    {
        $keysByKindAndIndex = $this->translationKeys($payloadsByLocale);
        $created = 0;

        foreach ($payloadsByLocale as $locale => $payload) {
            foreach (ExperienceEntry::KINDS as $kind) {
                $sections = $payload[$this->payloadKeyFor($kind)] ?? [];

                foreach (array_values($sections) as $index => $section) {
                    ExperienceEntry::query()->updateOrCreate(
                        [
                            'translation_key' => $keysByKindAndIndex[$kind][$index]
                                ?? "{$kind}-{$index}",
                            'locale' => $locale,
                        ],
                        [
                            'kind' => $kind,
                            'organisation' => (string) ($section['title'] ?? ''),
                            ...$this->splitEyebrow((string) ($section['eyebrow'] ?? '')),
                            'summary' => (string) ($section['summary'] ?? ''),
                            'paragraphs' => $section['paragraphs'] ?? [],
                            'detail_groups' => $section['detail_groups'] ?? [],
                            'position' => $index,
                        ],
                    );

                    $created++;
                }
            }
        }

        return $created;
    }

    /** The payload key each family is stored under. */
    public function payloadKeyFor(string $kind): string
    {
        return match ($kind) {
            'professional' => 'professional_sections',
            'side_project' => 'side_project_sections',
            'associative' => 'associative_sections',
            default => throw new \InvalidArgumentException("Unknown experience kind [{$kind}]."),
        };
    }

    /**
     * One key per position, shared by both languages.
     *
     * The English organisation names it, because a key has to be stable and
     * `Jewely E-commerce` and `Jewely Ecommerce` are the same job spelled
     * twice. Pairing by index is safe here and nowhere else: the two locales
     * are held in shape parity by a test, so the nth professional section in
     * French is the nth in English by construction.
     *
     * @param  array<string, array<string, mixed>>  $payloadsByLocale
     * @return array<string, array<int, string>>
     */
    protected function translationKeys(array $payloadsByLocale): array
    {
        $naming = $payloadsByLocale['en'] ?? reset($payloadsByLocale) ?: [];
        $keys = [];

        foreach (ExperienceEntry::KINDS as $kind) {
            foreach (array_values($naming[$this->payloadKeyFor($kind)] ?? []) as $index => $section) {
                $slug = Str::slug((string) ($section['title'] ?? ''));
                $keys[$kind][$index] = $slug === '' ? "{$kind}-{$index}" : $slug;
            }
        }

        return $keys;
    }

    /**
     * `Développeur e-commerce — 2023-2026` into its two halves.
     *
     * The same split `Projects.vue` was doing in the browser, done once here
     * and then never again. What it recovers is a role and a *label*, not a
     * role and a date: the right-hand side is kept verbatim so the page reads
     * the same, and a row only starts computing its own range once someone
     * clears that label.
     *
     * @return array{role: string, date_label: string|null}
     */
    protected function splitEyebrow(string $eyebrow): array
    {
        $parts = preg_split('/\s+[—–-]\s+/u', $eyebrow, 2) ?: [];

        if (count($parts) < 2) {
            return ['role' => trim($eyebrow), 'date_label' => null];
        }

        return [
            'role' => trim($parts[0]),
            'date_label' => trim($parts[1]) === '' ? null : trim($parts[1]),
        ];
    }
}

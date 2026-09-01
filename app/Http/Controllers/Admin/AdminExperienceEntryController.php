<?php

namespace App\Http\Controllers\Admin;

use App\Content\Schema\ExperienceSchemas;
use App\Http\Controllers\Controller;
use App\Models\ExperienceEntry;
use App\Models\User;
use App\Services\AdminAuditLogService;
use App\Services\ExperienceEntryRepository;
use App\Services\SiteSettingsService;
use App\Support\PublicLocale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

/**
 * The chronology, edited as rows.
 *
 * Every write here creates or touches one language of one position. The
 * pairing is the part worth stating: an entry is created in **both**
 * languages at once, with the second seeded from the first, because this site
 * holds its two locales in shape parity everywhere else and a chronology that
 * could grow a French-only row would break that on the first save an operator
 * made. Translating the sibling is an edit; creating it is not a decision the
 * operator should have to remember.
 */
class AdminExperienceEntryController extends Controller
{
    public function __construct(
        protected ExperienceEntryRepository $entries,
        protected SiteSettingsService $siteSettings,
        protected AdminAuditLogService $auditLogs,
    ) {}

    public function index(Request $request): Response
    {
        $locale = $this->resolveLocale($request->query('locale'));

        return Inertia::render('Admin/Experience/Index', [
            'locale' => $locale,
            'locales' => PublicLocale::supported(),
            'kinds' => ExperienceEntry::KINDS,
            'entries' => ExperienceEntry::query()
                ->where('locale', $locale)
                ->inChronologicalOrder()
                ->get()
                ->map(fn (ExperienceEntry $entry): array => [
                    'id' => $entry->id,
                    'translation_key' => $entry->translation_key,
                    'kind' => $entry->kind,
                    'organisation' => $entry->organisation,
                    'role' => $entry->role,
                    'date_range' => $this->entries->dateRangeFor($entry),
                    'is_current' => $entry->started_on !== null && $entry->ended_on === null,
                    'is_undated' => $entry->started_on === null,
                    'detail_group_count' => count($entry->detail_groups ?? []),
                ])
                ->values(),
        ]);
    }

    public function create(Request $request): Response
    {
        $locale = $this->resolveLocale($request->query('locale'));

        return Inertia::render('Admin/Experience/Edit', [
            'locale' => $locale,
            'schema' => ExperienceSchemas::entry()->toArray(),
            'recordFields' => ExperienceSchemas::RECORD_FIELDS,
            'entry' => null,
            'sibling' => null,
        ]);
    }

    public function edit(ExperienceEntry $entry): Response
    {
        return Inertia::render('Admin/Experience/Edit', [
            'locale' => $entry->locale,
            'schema' => ExperienceSchemas::entry()->toArray(),
            'recordFields' => ExperienceSchemas::RECORD_FIELDS,
            'entry' => $this->asForm($entry),
            'sibling' => $this->sibling($entry)?->only(['id', 'locale', 'organisation']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $locale = $this->resolveLocale($request->input('locale'));
        $values = $this->validated($request);

        $key = $this->uniqueTranslationKey($values['organisation'], $values['kind']);

        /**
         * Both languages, one save. The sibling starts as a copy rather than
         * as an empty row: an untranslated entry that still reads correctly
         * is a smaller failure than a blank one, and it shows the operator
         * exactly what there is to translate.
         */
        foreach (PublicLocale::supported() as $supported) {
            ExperienceEntry::query()->create([
                ...$values,
                'translation_key' => $key,
                'locale' => $supported,
                'position' => 0,
            ]);
        }

        $this->recordChange($request, 'experience.created', $key, $values['organisation']);

        return to_route('admin.experience.index', ['locale' => $locale])
            ->with('status', "Added [{$values['organisation']}] in both languages.");
    }

    public function update(Request $request, ExperienceEntry $entry): RedirectResponse
    {
        $entry->update($this->validated($request));

        $this->recordChange($request, 'experience.saved', $entry->translation_key, $entry->organisation);

        return to_route('admin.experience.index', ['locale' => $entry->locale])
            ->with('status', "Saved [{$entry->organisation}].");
    }

    /**
     * Removes a position from the chronology, in both languages.
     *
     * One language alone is never the right answer: a half-deleted entry
     * leaves the site saying different things about the same career depending
     * on which flag the reader clicked.
     */
    public function destroy(Request $request, ExperienceEntry $entry): RedirectResponse
    {
        $key = $entry->translation_key;
        $name = $entry->organisation;
        $locale = $entry->locale;

        ExperienceEntry::query()->where('translation_key', $key)->delete();

        $this->recordChange($request, 'experience.deleted', $key, $name);

        return to_route('admin.experience.index', ['locale' => $locale])
            ->with('status', "Removed [{$name}] from both languages.");
    }

    /**
     * Validated against the declaration, not only against Laravel's rules.
     *
     * The rules below catch the shape of a request; the declaration catches
     * the shape of the content — a repeating group missing its title, a
     * detail group holding a key nobody declared. Both refusals reach the
     * operator as sentences rather than as a 500.
     *
     * @return array<string, mixed>
     */
    protected function validated(Request $request): array
    {
        $values = $request->validate([
            'kind' => ['required', 'string', 'in:'.implode(',', ExperienceEntry::KINDS)],
            'organisation' => ['required', 'string', 'max:160'],
            'role' => ['required', 'string', 'max:160'],
            'started_on' => ['nullable', 'date'],
            'ended_on' => ['nullable', 'date', 'after_or_equal:started_on'],
            'date_label' => ['nullable', 'string', 'max:120'],
            'summary' => ['required', 'string', 'max:2000'],
            'paragraphs' => ['present', 'array'],
            'detail_groups' => ['present', 'array'],
        ]);

        $violations = ExperienceSchemas::entry()->violations(
            $this->withoutEmptyOptionals($values),
        );

        if ($violations !== []) {
            throw ValidationException::withMessages(['detail_groups' => $violations]);
        }

        return $values;
    }

    /**
     * Drops an optional key rather than handing the declaration a null.
     *
     * `optional()` says the key may be *missing*, not that its value may be
     * null — a present null still fails, and correctly so, because absent and
     * empty are different facts about a career. An entry with no end date is
     * current; an entry whose end date is the empty string is a typo.
     *
     * This is the same distinction the `ConvertEmptyStringsToNull` fix turned
     * on, seen from the other side: there the transport invented a null the
     * content never held, here the caller must not invent one either.
     *
     * @param  array<string, mixed>  $values
     * @return array<string, mixed>
     */
    protected function withoutEmptyOptionals(array $values): array
    {
        foreach (ExperienceSchemas::entry()->fields as $field) {
            if ($field->required) {
                continue;
            }

            if (($values[$field->name] ?? null) === null || ($values[$field->name] ?? null) === '') {
                unset($values[$field->name]);
            }
        }

        return $values;
    }

    /**
     * @return array<string, mixed>
     */
    protected function asForm(ExperienceEntry $entry): array
    {
        return [
            'id' => $entry->id,
            'locale' => $entry->locale,
            'translation_key' => $entry->translation_key,
            'kind' => $entry->kind,
            'organisation' => $entry->organisation,
            'role' => $entry->role,
            'started_on' => $entry->started_on?->format('Y-m-d'),
            'ended_on' => $entry->ended_on?->format('Y-m-d'),
            'date_label' => $entry->date_label,
            'summary' => $entry->summary,
            'paragraphs' => $entry->paragraphs ?? [],
            'detail_groups' => $entry->detail_groups ?? [],
        ];
    }

    protected function sibling(ExperienceEntry $entry): ?ExperienceEntry
    {
        return ExperienceEntry::query()
            ->where('translation_key', $entry->translation_key)
            ->where('locale', '!=', $entry->locale)
            ->first();
    }

    protected function uniqueTranslationKey(string $organisation, string $kind): string
    {
        $base = Str::slug($organisation) ?: $kind;
        $key = $base;
        $suffix = 2;

        while (ExperienceEntry::query()->where('translation_key', $key)->exists()) {
            $key = "{$base}-{$suffix}";
            $suffix++;
        }

        return $key;
    }

    protected function resolveLocale(mixed $locale): string
    {
        return in_array($locale, PublicLocale::supported(), true)
            ? (string) $locale
            : PublicLocale::supported()[0];
    }

    protected function recordChange(
        Request $request,
        string $action,
        string $key,
        string $organisation,
    ): void {
        $this->siteSettings->markRebuildRequired("Experience entry [{$organisation}] changed.");
        $this->auditLogs->recordAction(
            action: $action,
            subject: 'experience_entry',
            summary: ['translation_key' => $key, 'organisation' => $organisation],
            actor: $request->user() instanceof User ? $request->user() : null,
        );
    }
}

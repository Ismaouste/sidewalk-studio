<?php

namespace App\Services;

use App\Content\Schema\Field;
use App\Content\Schema\FieldType;
use App\Content\Schema\PageSchemas;
use App\Models\AdminAuditLog;
use App\Models\ExperienceEntry;
use App\Models\Page;
use App\Models\Publication;
use App\Support\PublicLocale;
use Illuminate\Support\Facades\Schema;

/**
 * What the back office should say when you open it.
 *
 * `/admin` used to land on Settings, which is the one screen an operator
 * needs least often and which answers no question they arrived with. This
 * answers the question they did arrive with — what is unfinished — and only
 * then says how much there is.
 *
 * Everything here is derived. Nothing is stored, nothing is cached, and every
 * count is guarded, so a deployment with no database renders an honest empty
 * dashboard rather than a crash or a lie.
 */
class AdminDashboardService
{
    public function __construct(
        protected QuestionnaireRepository $questionnaire,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function digest(): array
    {
        return [
            'attention' => $this->attention(),
            'record' => $this->record(),
            'activity' => $this->activity(),
        ];
    }

    /**
     * The unfinished, in the order it is worth doing.
     *
     * Each item names a number, a place to go, and — this is the part that
     * makes it a dashboard rather than a badge — what the site currently does
     * about it. An operator should never have to guess whether an unfinished
     * thing is visibly broken or quietly waiting.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function attention(): array
    {
        $items = [];

        $open = $this->questionnaire->unansweredCount();

        if ($open > 0) {
            $items[] = [
                'key' => 'questionnaire',
                'count' => $open,
                'label' => $open === 1 ? 'unanswered question' : 'unanswered questions',
                'consequence' => 'Their marginal notes are simply absent from the chronology.',
                'href' => '/admin/questionnaire',
            ];
        }

        $undated = $this->countExperience(fn ($query) => $query->whereNull('started_on'));

        if ($undated > 0) {
            $items[] = [
                'key' => 'undated-experience',
                'count' => $undated,
                'label' => $undated === 1 ? 'position with no start date' : 'positions with no start date',
                'consequence' => 'They close the chronology and still show their written label.',
                'href' => '/admin/experience',
            ];
        }

        $unfilled = $this->unfilledPageSlots();

        if ($unfilled !== []) {
            $total = array_sum(array_column($unfilled, 'count'));
            $items[] = [
                'key' => 'unfilled-slots',
                'count' => $total,
                'label' => $total === 1 ? 'declared slot nobody has filled' : 'declared slots nobody has filled',
                'consequence' => 'Usually a field added to the schema after the page was written.',
                'href' => '/admin/pages',
                'detail' => $unfilled,
            ];
        }

        $drafts = $this->countPublications(fn ($query) => $query->where('status', 'draft'));

        if ($drafts > 0) {
            $items[] = [
                'key' => 'drafts',
                'count' => $drafts,
                'label' => $drafts === 1 ? 'unpublished entry' : 'unpublished entries',
                'consequence' => 'Held back from the journal and from the sitemap.',
                'href' => '/admin/publications',
            ];
        }

        return $items;
    }

    /**
     * @return array<string, mixed>
     */
    protected function record(): array
    {
        return [
            'positions' => $this->countExperience(fn ($query) => $query->where('locale', PublicLocale::supported()[0])),
            'current' => $this->countExperience(
                fn ($query) => $query
                    ->where('locale', PublicLocale::supported()[0])
                    ->whereNotNull('started_on')
                    ->whereNull('ended_on'),
            ),
            'published' => $this->countPublications(fn ($query) => $query->where('status', 'published')),
            'pages' => Schema::hasTable('pages') ? Page::query()->count() : 0,
            'locales' => PublicLocale::supported(),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function activity(): array
    {
        if (! Schema::hasTable('admin_audit_logs')) {
            return [];
        }

        return AdminAuditLog::query()
            ->latest('id')
            ->limit(6)
            ->get()
            ->map(fn (AdminAuditLog $log): array => [
                'id' => $log->id,
                'action' => $log->action,
                'subject' => $log->subject,
                'actor' => $log->actor_name,
                'at' => $log->created_at?->toDateTimeString(),
            ])
            ->all();
    }

    /**
     * Declared leaves that hold an empty string, per page and language.
     *
     * The same count the page editor shows at the top of a form, computed on
     * the server so the dashboard can name it before anyone opens the page.
     * It walks the declaration rather than the payload, which is the only way
     * round: a slot nobody has filled is by definition not interesting to the
     * content, only to the schema.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function unfilledPageSlots(): array
    {
        if (! Schema::hasTable('pages')) {
            return [];
        }

        $unfilled = [];

        foreach (Page::query()->get() as $page) {
            $count = $this->countEmptyLeaves(
                PageSchemas::for($page->page_key)->fields,
                is_array($page->payload) ? $page->payload : [],
            );

            if ($count > 0) {
                $unfilled[] = [
                    'page_key' => $page->page_key,
                    'locale' => $page->locale,
                    'count' => $count,
                ];
            }
        }

        usort($unfilled, fn (array $a, array $b): int => $b['count'] <=> $a['count']);

        return $unfilled;
    }

    /**
     * @param  array<int, Field>  $fields
     * @param  array<string, mixed>  $value
     */
    protected function countEmptyLeaves(array $fields, array $value): int
    {
        $count = 0;

        foreach ($fields as $field) {
            if (! array_key_exists($field->name, $value)) {
                continue;
            }

            $count += $this->countEmptyIn($field, $value[$field->name]);
        }

        return $count;
    }

    protected function countEmptyIn(Field $field, mixed $value): int
    {
        if ($field->repeats) {
            if (! is_array($value)) {
                return 0;
            }

            return array_sum(array_map(
                fn (mixed $item): int => $this->countEmptyLeaf($field, $item),
                $value,
            ));
        }

        return $this->countEmptyLeaf($field, $value);
    }

    protected function countEmptyLeaf(Field $field, mixed $value): int
    {
        if ($field->type === FieldType::Group) {
            return is_array($value) ? $this->countEmptyLeaves($field->children, $value) : 0;
        }

        return $value === '' ? 1 : 0;
    }

    protected function countExperience(callable $scope): int
    {
        if (! Schema::hasTable('experience_entries')) {
            return 0;
        }

        return $scope(ExperienceEntry::query())->count();
    }

    protected function countPublications(callable $scope): int
    {
        if (! Schema::hasTable('publications')) {
            return 0;
        }

        return $scope(Publication::query())->count();
    }
}

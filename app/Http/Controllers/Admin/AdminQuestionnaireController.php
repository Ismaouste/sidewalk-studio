<?php

namespace App\Http\Controllers\Admin;

use App\Content\Questionnaire\PoeticQuestions;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AdminAuditLogService;
use App\Services\QuestionnaireRepository;
use App\Services\SiteSettingsService;
use App\Support\PublicLocale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * The questions the site asks its owner.
 *
 * One screen, every question, both languages side by side — because these are
 * short and because a translation written next to its original is a better
 * translation than one written from memory a screen away. The whole set saves
 * at once for the same reason: answering three of four and losing them to a
 * navigation is the failure this shape cannot have.
 */
class AdminQuestionnaireController extends Controller
{
    public function __construct(
        protected QuestionnaireRepository $questionnaire,
        protected SiteSettingsService $siteSettings,
        protected AdminAuditLogService $auditLogs,
    ) {}

    public function index(): Response
    {
        return Inertia::render('Admin/Questionnaire/Index', [
            'locales' => PublicLocale::supported(),
            'questions' => $this->questionnaire->adminList(),
            'unanswered' => $this->questionnaire->unansweredCount(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'answers' => ['present', 'array'],
            'answers.*' => ['array'],
            /**
             * Capped at a length the caption can actually hold. The limit is
             * a design constraint rather than a storage one: the answer lands
             * in a micro-typographic marginal note, and a paragraph there
             * does not read as a marginal note any more.
             */
            'answers.*.*' => ['nullable', 'string', 'max:280'],
        ]);

        $saved = 0;

        foreach ($validated['answers'] as $key => $byLocale) {
            if (! PoeticQuestions::has((string) $key)) {
                continue;
            }

            $this->questionnaire->save((string) $key, array_intersect_key(
                array_map(fn ($answer): string => (string) ($answer ?? ''), $byLocale),
                array_flip(PublicLocale::supported()),
            ));

            $saved++;
        }

        $this->siteSettings->markRebuildRequired('Questionnaire answers changed.');
        $this->auditLogs->recordAction(
            action: 'questionnaire.saved',
            subject: 'questionnaire',
            summary: ['questions' => $saved],
            actor: $request->user() instanceof User ? $request->user() : null,
        );

        return to_route('admin.questionnaire.index')
            ->with('status', 'Answers saved.');
    }
}

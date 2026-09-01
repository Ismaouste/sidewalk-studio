<?php

namespace App\Services;

use App\Content\Questionnaire\PoeticQuestions;
use App\Models\QuestionnaireAnswer;
use App\Support\PublicLocale;
use Illuminate\Support\Facades\Schema;

/**
 * What the owner has answered, and what is still open.
 *
 * Unanswered is the resting state, not an error. Every read here returns
 * whatever exists and nothing more, so a page asking for a marginal note gets
 * an empty list until there is something to say — which is what lets this
 * ship before a single question has been answered, and what lets production,
 * which carries no database at all, render exactly as it did.
 */
class QuestionnaireRepository
{
    /**
     * @return array<string, string>
     */
    public function answers(string $locale): array
    {
        if (! Schema::hasTable('questionnaire_answers')) {
            return [];
        }

        return QuestionnaireAnswer::query()
            ->where('locale', $locale)
            ->pluck('answer', 'question_key')
            ->filter(fn (?string $answer): bool => filled($answer))
            ->all();
    }

    /**
     * The answered questions for one surface, in declaration order.
     *
     * Order is editorial and it is the declaration's, not the database's: the
     * first question is the one meant to land beside the most recent
     * position. Skipping the unanswered rather than leaving a hole is what
     * keeps a half-finished questionnaire from looking like a bug.
     *
     * @return array<int, array{key: string, quote: string, prompt: string}>
     */
    public function marginaliaFor(string $surface, string $locale): array
    {
        $answers = $this->answers($locale);
        $notes = [];

        foreach (PoeticQuestions::surfacing($surface) as $question) {
            $answer = trim((string) ($answers[$question['key']] ?? ''));

            if ($answer === '') {
                continue;
            }

            $notes[] = [
                'key' => $question['key'],
                'quote' => $answer,
                'prompt' => PoeticQuestions::prompt($question['key']),
            ];
        }

        return $notes;
    }

    /**
     * Every declared question with whatever each language has said, for the
     * admin.
     *
     * Built from the declaration rather than from the table, so a question
     * added in code appears here unanswered instead of being invisible until
     * someone happens to write a row for it.
     *
     * @return array<int, array<string, mixed>>
     */
    public function adminList(): array
    {
        $byLocale = [];

        foreach (PublicLocale::supported() as $locale) {
            $byLocale[$locale] = $this->answers($locale);
        }

        return array_map(
            fn (array $question): array => [
                'key' => $question['key'],
                'surface' => $question['surface'],
                'hint' => PoeticQuestions::hint($question['key']),
                'prompts' => $this->promptsFor($question['key']),
                'answers' => array_map(
                    fn (array $answers): string => (string) ($answers[$question['key']] ?? ''),
                    $byLocale,
                ),
            ],
            PoeticQuestions::all(),
        );
    }

    public function unansweredCount(): int
    {
        $open = 0;

        foreach ($this->adminList() as $question) {
            foreach ($question['answers'] as $answer) {
                if (trim($answer) === '') {
                    $open++;
                }
            }
        }

        return $open;
    }

    /**
     * Writes one question in every language at once.
     *
     * An empty answer deletes the row rather than storing a blank. The two
     * are the same fact — nothing has been said — and keeping only one of
     * them means no read anywhere has to know the difference between an
     * absent row and a present empty string. That distinction is exactly what
     * made four admin pages unsaveable elsewhere in this codebase.
     *
     * @param  array<string, string>  $answersByLocale
     */
    public function save(string $key, array $answersByLocale): void
    {
        foreach ($answersByLocale as $locale => $answer) {
            $answer = trim($answer);

            if ($answer === '') {
                QuestionnaireAnswer::query()
                    ->where('question_key', $key)
                    ->where('locale', $locale)
                    ->delete();

                continue;
            }

            QuestionnaireAnswer::query()->updateOrCreate(
                ['question_key' => $key, 'locale' => $locale],
                ['answer' => $answer, 'answered_at' => now()],
            );
        }
    }

    /**
     * @return array<string, string>
     */
    protected function promptsFor(string $key): array
    {
        $prompts = [];
        $current = app()->getLocale();

        foreach (PublicLocale::supported() as $locale) {
            app()->setLocale($locale);
            $prompts[$locale] = PoeticQuestions::prompt($key);
        }

        app()->setLocale($current);

        return $prompts;
    }
}

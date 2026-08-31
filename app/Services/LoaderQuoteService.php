<?php

namespace App\Services;

use App\Models\LoaderQuote;
use Illuminate\Support\Facades\Schema;

class LoaderQuoteService
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function activeForLocale(string $locale, ?string $theme = null): array
    {
        if (! Schema::hasTable('loader_quotes')) {
            return [];
        }

        return LoaderQuote::query()
            ->where('locale', $locale)
            ->where('is_active', true)
            ->when($theme !== null, fn ($query) => $query->where(function ($subquery) use ($theme): void {
                $subquery->whereNull('theme_target')
                    ->orWhere('theme_target', $theme);
            }))
            ->orderByDesc('weight')
            ->orderBy('id')
            ->get()
            ->map(fn (LoaderQuote $quote): array => $this->shape($quote))
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function adminList(): array
    {
        if (! Schema::hasTable('loader_quotes')) {
            return [];
        }

        return LoaderQuote::query()
            ->orderBy('locale')
            ->orderByDesc('weight')
            ->orderBy('id')
            ->get()
            ->map(fn (LoaderQuote $quote): array => $this->shape($quote))
            ->all();
    }

    /**
     * @param  array<int, array<string, mixed>>  $quotes
     */
    public function saveMany(array $quotes): void
    {
        if (! Schema::hasTable('loader_quotes')) {
            return;
        }

        $ids = [];

        foreach ($quotes as $quote) {
            $record = LoaderQuote::query()->updateOrCreate(
                ['id' => $quote['id'] ?? null],
                [
                    'text' => $quote['text'],
                    'type' => $quote['type'],
                    'author' => filled($quote['author'] ?? null) ? $quote['author'] : null,
                    'locale' => $quote['locale'],
                    'is_active' => (bool) $quote['is_active'],
                    'theme_target' => filled($quote['theme_target'] ?? null) ? $quote['theme_target'] : null,
                    'weight' => (int) ($quote['weight'] ?? 0),
                ],
            );

            $ids[] = $record->id;
        }

        LoaderQuote::query()
            ->whereNotIn('id', $ids)
            ->delete();
    }

    /**
     * @return array<string, mixed>
     */
    protected function shape(LoaderQuote $quote): array
    {
        return [
            'id' => $quote->id,
            'text' => $quote->text,
            'type' => $quote->type,
            'author' => $quote->author,
            'locale' => $quote->locale,
            'is_active' => $quote->is_active,
            'theme_target' => $quote->theme_target,
            'weight' => $quote->weight,
        ];
    }
}

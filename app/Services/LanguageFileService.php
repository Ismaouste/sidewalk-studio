<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use RuntimeException;

class LanguageFileService
{
    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function all(): Collection
    {
        return collect($this->managedFiles())
            ->map(fn (array $file): array => [
                ...$file,
                'payload' => $this->load($file['key']),
            ])
            ->values();
    }

    /**
     * @return array<string, mixed>
     */
    public function load(string $key): array
    {
        $definition = $this->definition($key);
        $payload = require $definition['path'];

        if (! is_array($payload)) {
            throw new RuntimeException("Language file [{$definition['path']}] did not return an array.");
        }

        return $payload;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function save(string $key, array $payload): void
    {
        $definition = $this->definition($key);
        $export = "<?php\n\nreturn ".$this->exportArray($payload).";\n";

        File::put($definition['path'], $export);
    }

    /**
     * @return array<int, array{key: string, label: string, locale: string, path: string}>
     */
    protected function managedFiles(): array
    {
        return [
            [
                'key' => 'en.site',
                'label' => 'English site copy',
                'locale' => 'en',
                'path' => lang_path('en/site.php'),
            ],
            [
                'key' => 'fr.site',
                'label' => 'French site copy',
                'locale' => 'fr',
                'path' => lang_path('fr/site.php'),
            ],
        ];
    }

    /**
     * @return array{key: string, label: string, locale: string, path: string}
     */
    protected function definition(string $key): array
    {
        $definition = collect($this->managedFiles())
            ->firstWhere('key', $key);

        if (! is_array($definition)) {
            throw new RuntimeException("Unsupported managed language file [{$key}].");
        }

        return $definition;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    protected function exportArray(array $payload, int $depth = 0): string
    {
        $indent = str_repeat('    ', $depth);
        $nextIndent = str_repeat('    ', $depth + 1);
        $lines = ['['];

        foreach ($payload as $key => $value) {
            $serializedKey = var_export($key, true);
            $serializedValue = is_array($value)
                ? $this->exportArray($value, $depth + 1)
                : var_export($value, true);

            $lines[] = "{$nextIndent}{$serializedKey} => {$serializedValue},";
        }

        $lines[] = "{$indent}]";

        return implode("\n", $lines);
    }
}

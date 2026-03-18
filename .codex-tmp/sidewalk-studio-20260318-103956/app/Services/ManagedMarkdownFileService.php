<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use RuntimeException;
use Spatie\YamlFrontMatter\YamlFrontMatter;
use Symfony\Component\Yaml\Yaml;

class ManagedMarkdownFileService
{
    /**
     * @return array{matter: array<string, mixed>, body: string}
     */
    public function read(string $path): array
    {
        if (! File::exists($path)) {
            throw new RuntimeException("Markdown source [{$path}] does not exist.");
        }

        $document = YamlFrontMatter::parseFile($path);

        return [
            'matter' => $document->matter(),
            'body' => $this->normalizeBody($document->body()),
        ];
    }

    /**
     * @param  array<string, mixed>  $matter
     */
    public function write(string $path, array $matter, string $body): void
    {
        File::ensureDirectoryExists(dirname($path));

        $contents = $this->render($matter, $body);
        $temporaryPath = $path.'.tmp';

        File::put($temporaryPath, $contents);

        if (File::exists($path)) {
            File::delete($path);
        }

        File::move($temporaryPath, $path);
    }

    public function move(string $from, string $to): void
    {
        if ($from === $to || ! File::exists($from)) {
            return;
        }

        File::ensureDirectoryExists(dirname($to));
        File::move($from, $to);
    }

    /**
     * @param  array<string, mixed>  $matter
     */
    public function render(array $matter, string $body): string
    {
        $normalizedMatter = $this->normalizeMatter($matter);
        $normalizedBody = $this->normalizeBody($body);

        if ($normalizedMatter === []) {
            return $normalizedBody === '' ? '' : $normalizedBody.PHP_EOL;
        }

        $yaml = Yaml::dump($normalizedMatter, 4, 2, Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK);

        return trim("---\n{$yaml}---\n\n".$normalizedBody).PHP_EOL;
    }

    /**
     * @param  array<string, mixed>  $matter
     * @return array<string, mixed>
     */
    protected function normalizeMatter(array $matter): array
    {
        $ordered = [];

        foreach (['title', 'publication_type', 'page_key', 'locale', 'updated_at', 'managed_by'] as $key) {
            if (array_key_exists($key, $matter) && $matter[$key] !== null && $matter[$key] !== '') {
                $ordered[$key] = $matter[$key];
            }
        }

        foreach ($matter as $key => $value) {
            if (array_key_exists($key, $ordered) || $value === null || $value === '') {
                continue;
            }

            $ordered[$key] = $value;
        }

        return $ordered;
    }

    protected function normalizeBody(string $body): string
    {
        return Str::of(str_replace("\r\n", "\n", $body))
            ->trim()
            ->toString();
    }
}

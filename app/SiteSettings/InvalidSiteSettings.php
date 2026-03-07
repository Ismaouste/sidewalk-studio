<?php

namespace App\SiteSettings;

use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class InvalidSiteSettings extends RuntimeException
{
    public static function fromValidationException(ValidationException $exception): self
    {
        $messages = Collection::make($exception->errors())
            ->map(fn (array $errors, string $field): string => sprintf('%s: %s', $field, implode(', ', $errors)))
            ->implode('; ');

        return new self(
            'Invalid site settings payload. '.$messages,
            previous: $exception,
        );
    }
}

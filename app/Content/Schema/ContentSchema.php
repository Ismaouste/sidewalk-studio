<?php

namespace App\Content\Schema;

/**
 * The declaration for one page key or one publication type.
 *
 * This is the single source the plan describes: the save path validates
 * against it, the seeder knows what to seed from it, the admin generates its
 * form from it, and the locale parity check compares two payloads through it.
 * Nothing else may hold a second opinion about the shape of the content.
 *
 * It is data. Adding a slot is an entry here plus a branch in the template —
 * a small, reviewable change — and the editor regenerates itself.
 */
final class ContentSchema
{
    /**
     * @param  array<int, Field>  $fields
     */
    public function __construct(
        public readonly string $key,
        public readonly string $label,
        public readonly array $fields,
    ) {}

    /**
     * Every way `$payload` fails this declaration, as sentences. An empty
     * array means it validates.
     *
     * Undeclared keys are violations too, and that is the half that is easy
     * to leave out. A schema that only checks what it knows about cannot
     * catch a misspelt key: the correct one reads as missing, the typo reads
     * as nothing at all, and the operator gets one confusing message instead
     * of two clear ones.
     *
     * @param  array<string, mixed>  $payload
     * @return array<int, string>
     */
    public function violations(array $payload, string $path = ''): array
    {
        $violations = [];
        $declared = [];

        foreach ($this->fields as $field) {
            $declared[] = $field->name;
            $fieldPath = $path === '' ? $field->name : "{$path}.{$field->name}";

            if (! array_key_exists($field->name, $payload)) {
                if ($field->required) {
                    $violations[] = "[{$fieldPath}] is required and missing.";
                }

                continue;
            }

            $violations = [
                ...$violations,
                ...$field->violations($payload[$field->name], $fieldPath),
            ];
        }

        foreach (array_keys($payload) as $key) {
            if (! in_array((string) $key, $declared, true)) {
                $keyPath = $path === '' ? (string) $key : "{$path}.{$key}";
                $violations[] = "[{$keyPath}] is not declared in the [{$this->key}] schema.";
            }
        }

        return $violations;
    }

    /**
     * The shape of `$payload`, with every leaf replaced by the name of its
     * type and every list by its length. Two locales of the same page key
     * must produce the same shape, and comparing these is how the runtime
     * parity check says *which* field drifted rather than just that one did.
     *
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function shapeOf(array $payload): array
    {
        $shape = [];

        foreach ($this->fields as $field) {
            if (! array_key_exists($field->name, $payload)) {
                continue;
            }

            $shape[$field->name] = self::describeShape($payload[$field->name]);
        }

        return $shape;
    }

    protected static function describeShape(mixed $value): mixed
    {
        if (! is_array($value)) {
            return get_debug_type($value);
        }

        if ($value === [] || array_is_list($value)) {
            return [
                'count' => count($value),
                'items' => array_map(self::describeShape(...), $value),
            ];
        }

        return array_map(self::describeShape(...), $value);
    }

    public function field(string $name): ?Field
    {
        foreach ($this->fields as $field) {
            if ($field->name === $name) {
                return $field;
            }
        }

        return null;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'label' => $this->label,
            'fields' => array_map(fn (Field $field): array => $field->toArray(), $this->fields),
        ];
    }
}

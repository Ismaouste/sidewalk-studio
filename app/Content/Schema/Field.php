<?php

namespace App\Content\Schema;

use DateTimeInterface;
use InvalidArgumentException;

/**
 * One declared field.
 *
 * A field says four things the code needed and did not have: what type the
 * value is, whether it has to be there, whether it repeats, and — when it
 * repeats over groups — which of the group's own fields names an item well
 * enough to be a `<summary>` an operator can scan.
 *
 * Fields are immutable and built by chaining, so a declaration reads as a
 * sentence:
 *
 *     Field::group('detail_groups', [
 *         Field::line('title'),
 *         Field::line('pills')->repeating(),
 *     ])->repeating(itemLabel: 'title')->optional()
 */
final class Field
{
    /**
     * @param  array<int, self>  $children
     * @param  array<int, string>  $choices
     */
    private function __construct(
        public readonly string $name,
        public readonly FieldType $type,
        public readonly bool $required = true,
        public readonly bool $repeats = false,
        public readonly ?string $itemLabel = null,
        public readonly array $children = [],
        public readonly array $choices = [],
        public readonly string $label = '',
        public readonly string $help = '',
    ) {}

    public static function line(string $name, string $label = ''): self
    {
        return new self($name, FieldType::Line, label: $label);
    }

    public static function text(string $name, string $label = ''): self
    {
        return new self($name, FieldType::Text, label: $label);
    }

    public static function markdown(string $name, string $label = ''): self
    {
        return new self($name, FieldType::Markdown, label: $label);
    }

    public static function slug(string $name, string $label = ''): self
    {
        return new self($name, FieldType::Slug, label: $label);
    }

    public static function date(string $name, string $label = ''): self
    {
        return new self($name, FieldType::Date, label: $label);
    }

    public static function url(string $name, string $label = ''): self
    {
        return new self($name, FieldType::Url, label: $label);
    }

    /**
     * @param  array<int, string>  $choices
     */
    public static function choice(string $name, array $choices, string $label = ''): self
    {
        return new self($name, FieldType::Choice, choices: $choices, label: $label);
    }

    /**
     * @param  array<int, self>  $children
     */
    public static function group(string $name, array $children, string $label = ''): self
    {
        return new self($name, FieldType::Group, children: $children, label: $label);
    }

    public function optional(): self
    {
        return $this->with(required: false);
    }

    /**
     * `$itemLabel` names the child field whose value summarises an item. It is
     * what turns eighteen inputs into three named rows in the editor, so a
     * repeating group without one is a declaration bug rather than a style
     * choice — and it is rejected here rather than discovered on a phone.
     */
    public function repeating(?string $itemLabel = null): self
    {
        if ($this->type === FieldType::Group && $itemLabel === null) {
            throw new InvalidArgumentException(
                "Repeating group [{$this->name}] needs an itemLabel: the editor "
                .'collapses each item into a <summary>, and without one the '
                .'operator sees a list of identical rows.',
            );
        }

        if ($itemLabel !== null && ! $this->hasChild($itemLabel)) {
            throw new InvalidArgumentException(
                "Repeating group [{$this->name}] names [{$itemLabel}] as its "
                .'item label, but has no such field.',
            );
        }

        return $this->with(repeats: true, itemLabel: $itemLabel);
    }

    public function withHelp(string $help): self
    {
        return $this->with(help: $help);
    }

    public function hasChild(string $name): bool
    {
        foreach ($this->children as $child) {
            if ($child->name === $name) {
                return true;
            }
        }

        return false;
    }

    /**
     * Every way `$value` fails this declaration, as sentences naming the path
     * that failed. An empty array means it validates.
     *
     * @return array<int, string>
     */
    public function violations(mixed $value, string $path): array
    {
        if (! $this->repeats) {
            return $this->violationsForOne($value, $path);
        }

        if (! is_array($value) || ($value !== [] && ! array_is_list($value))) {
            return ["[{$path}] should be a list of {$this->type->value} values, got ".$this->describe($value).'.'];
        }

        $violations = [];

        foreach ($value as $index => $item) {
            $violations = [
                ...$violations,
                ...$this->violationsForOne($item, "{$path}.{$index}"),
            ];
        }

        return $violations;
    }

    /**
     * @return array<int, string>
     */
    protected function violationsForOne(mixed $value, string $path): array
    {
        if ($this->type === FieldType::Group) {
            return $this->violationsForGroup($value, $path);
        }

        return $this->violationsForScalar($value, $path);
    }

    /**
     * @return array<int, string>
     */
    protected function violationsForGroup(mixed $value, string $path): array
    {
        if (! is_array($value) || array_is_list($value)) {
            return ["[{$path}] should be a group, got ".$this->describe($value).'.'];
        }

        $violations = [];
        $declared = [];

        foreach ($this->children as $child) {
            $declared[] = $child->name;

            if (! array_key_exists($child->name, $value)) {
                if ($child->required) {
                    $violations[] = "[{$path}.{$child->name}] is required and missing.";
                }

                continue;
            }

            $violations = [
                ...$violations,
                ...$child->violations($value[$child->name], "{$path}.{$child->name}"),
            ];
        }

        foreach (array_keys($value) as $key) {
            if (! in_array((string) $key, $declared, true)) {
                $violations[] = "[{$path}.{$key}] is not declared.";
            }
        }

        return $violations;
    }

    /**
     * The single check that would have caught the defect this feature was
     * opened for: a paragraph declared as a line, holding a colon-space, that
     * YAML resolved into a one-key mapping. `is_string` rejects it, at save
     * time and in CI, without anyone having to read the file.
     *
     * @return array<int, string>
     */
    protected function violationsForScalar(mixed $value, string $path): array
    {
        $expected = $this->type->value;

        if ($this->type === FieldType::Date) {
            /**
             * An unquoted ISO date in YAML resolves to a Unix timestamp, so
             * the parser hands back an `int` for most of the content in this
             * repository and a `string` only where the author quoted it.
             * Both are the same date; rejecting either would fail thirty
             * valid files.
             */
            return match (true) {
                is_int($value), $value instanceof DateTimeInterface => [],
                is_string($value) && strtotime($value) !== false => [],
                default => ["[{$path}] should be a date, got ".$this->describe($value).'.'],
            };
        }

        if (! is_string($value)) {
            return ["[{$path}] should be a {$expected}, got ".$this->describe($value).'.'];
        }

        if ($this->type === FieldType::Choice && ! in_array($value, $this->choices, true)) {
            return [
                "[{$path}] should be one of ".implode(', ', $this->choices)
                .", got [{$value}].",
            ];
        }

        if ($this->type === FieldType::Slug && preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $value) !== 1) {
            return ["[{$path}] should be a lowercase hyphenated slug, got [{$value}]."];
        }

        if ($this->type === FieldType::Line && str_contains($value, "\n")) {
            return ["[{$path}] should be a single line, but contains a line break."];
        }

        return [];
    }

    protected function describe(mixed $value): string
    {
        if (is_array($value)) {
            return array_is_list($value) ? 'a list' : 'a mapping ('.implode(', ', array_map(
                fn ($key): string => (string) $key,
                array_slice(array_keys($value), 0, 2),
            )).')';
        }

        return get_debug_type($value);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'type' => $this->type->value,
            'label' => $this->label !== '' ? $this->label : null,
            'help' => $this->help !== '' ? $this->help : null,
            'required' => $this->required,
            'repeats' => $this->repeats,
            'itemLabel' => $this->itemLabel,
            'choices' => $this->choices !== [] ? $this->choices : null,
            'children' => $this->children !== []
                ? array_map(fn (self $child): array => $child->toArray(), $this->children)
                : null,
        ], fn (mixed $value): bool => $value !== null);
    }

    /**
     * @param  array<int, self>|null  $children
     * @param  array<int, string>|null  $choices
     */
    protected function with(
        ?bool $required = null,
        ?bool $repeats = null,
        ?string $itemLabel = null,
        ?array $children = null,
        ?array $choices = null,
        ?string $label = null,
        ?string $help = null,
    ): self {
        return new self(
            $this->name,
            $this->type,
            $required ?? $this->required,
            $repeats ?? $this->repeats,
            $itemLabel ?? $this->itemLabel,
            $children ?? $this->children,
            $choices ?? $this->choices,
            $label ?? $this->label,
            $help ?? $this->help,
        );
    }
}

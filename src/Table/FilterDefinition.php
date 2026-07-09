<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Table;

use JsonSerializable;
use Middag\Ui\Shared\Enum\FilterType;
use Middag\Ui\Shared\ValueObject\Label;
use Middag\Ui\Shared\ValueObject\Translatable;

/**
 * Typed definition of a single table filter control.
 *
 * @api
 */
final readonly class FilterDefinition implements JsonSerializable
{
    /**
     * @param array<int, array{value: mixed, label: string|Translatable}> $options
     */
    public function __construct(
        public string $key,
        public string|Translatable $label,
        public FilterType $type = FilterType::Select,
        public array $options = [],
        public string|Translatable|null $placeholder = null,
        public mixed $default = null,
    ) {}

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        $payload = [
            'key' => $this->key,
            'label' => Label::serialize($this->label),
            'type' => $this->type->value,
        ];

        if ($this->options !== []) {
            $payload['options'] = array_map(
                static fn (array $option): array => [
                    'value' => $option['value'],
                    'label' => Label::serialize($option['label']),
                ],
                $this->options,
            );
        }

        if ($this->placeholder !== null) {
            $payload['placeholder'] = Label::serializeNullable($this->placeholder);
        }

        if ($this->default !== null) {
            $payload['default'] = $this->default;
        }

        return $payload;
    }

    /** @return array<string, mixed> */
    public static function jsonSchema(): array
    {
        return [
            'type' => 'object',
            'required' => ['key', 'label', 'type'],
            'properties' => [
                'key' => ['type' => 'string'],
                'label' => ['$ref' => '#/$defs/Label'],
                'type' => ['$ref' => '#/$defs/FilterType'],
                'options' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'required' => ['value', 'label'],
                        // value is mixed (any JSON value): `true` = accept-anything schema.
                        'properties' => ['value' => true, 'label' => ['$ref' => '#/$defs/Label']],
                        'additionalProperties' => false,
                    ],
                ],
                'placeholder' => ['$ref' => '#/$defs/Label'],
                // default is mixed (any JSON value).
                'default' => true,
            ],
            'additionalProperties' => false,
        ];
    }
}

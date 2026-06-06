<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Form;

/**
 * Props for components that render a list of options (select, radio,
 * multiselect, native_select, entity_picker): {@see FieldPropsBase} plus an
 * optional `options` list.
 *
 * A schema-only value shape (no instances) — composed via `allOf` so it stays
 * open for further intersection (see {@see FieldPropsBase} on the pitfall).
 * Mirrors @middag-io/react's `OptionFieldProps`.
 *
 * @api
 */
final class OptionFieldProps
{
    private function __construct() {}

    /** @return array<string, mixed> */
    public static function jsonSchema(): array
    {
        return [
            'allOf' => [
                ['$ref' => '#/$defs/FieldPropsBase'],
                [
                    'type' => 'object',
                    'properties' => [
                        'options' => [
                            'type' => 'array',
                            'items' => [
                                'type' => 'object',
                                'required' => ['value', 'label'],
                                'properties' => [
                                    'value' => ['type' => 'string'],
                                    'label' => ['type' => 'string'],
                                ],
                                'additionalProperties' => false,
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}

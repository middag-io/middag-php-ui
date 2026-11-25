<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Block;

/**
 * One filter a `dense_table` block offers in its toolbar.
 *
 * A schema-only value shape (no instances): it types block data for schema
 * consumers. {@see BlockBuilder} keeps taking plain arrays.
 *
 * @api
 */
class DenseTableFilterDef
{
    private function __construct() {}

    /** @return array<string, mixed> */
    public static function jsonSchema(): array
    {
        return [
            'type' => 'object',
            'required' => ['key', 'label', 'type'],
            'properties' => [
                'key' => ['type' => 'string'],
                'label' => ['type' => 'string'],
                'type' => [
                    'enum' => ['select', 'multiselect', 'date_range'],
                ],
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
            'additionalProperties' => false,
        ];
    }
}

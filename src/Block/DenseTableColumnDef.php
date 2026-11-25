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

use Middag\Ui\Table\Column;

/**
 * One column of a `dense_table` block. `variant` is open: a known {@see
 * \Middag\Ui\Shared\Enum\ColumnVariant} value, or another string the client
 * renders as plain text. Carries the client-side display hints (`variant`,
 * `timestampFormat`, `href`, `entity`, widths, `statusMap`) and, optionally,
 * the fields a serialized {@see Column} adds (`format`,
 * `formatOptions`, `options`).
 *
 * A schema-only value shape (no instances): it types block data for schema
 * consumers. {@see BlockBuilder} keeps taking plain arrays.
 *
 * @api
 */
final class DenseTableColumnDef
{
    private function __construct() {}

    /** @return array<string, mixed> */
    public static function jsonSchema(): array
    {
        return [
            'type' => 'object',
            'required' => ['key', 'label'],
            'properties' => [
                'key' => ['type' => 'string'],
                'label' => ['type' => 'string'],
                'sortable' => ['type' => 'boolean'],
                'searchable' => ['type' => 'boolean'],
                'variant' => [
                    'anyOf' => [
                        ['$ref' => '#/$defs/ColumnVariant'],
                        ['type' => 'string'],
                    ],
                ],
                'timestampFormat' => [
                    'enum' => ['relative', 'datetime', 'date', 'time', 'iso'],
                ],
                'href' => ['type' => 'string'],
                'entity' => ['$ref' => '#/$defs/EntityRef'],
                'minWidth' => ['type' => 'number'],
                'maxWidth' => ['type' => 'number'],
                'statusMap' => [
                    'type' => 'object',
                    'additionalProperties' => [
                        'enum' => ['success', 'warning', 'destructive', 'secondary', 'info', 'default'],
                    ],
                ],
                'format' => ['$ref' => '#/$defs/ValueFormat'],
                'formatOptions' => [
                    'type' => 'object',
                    'additionalProperties' => true,
                ],
                'options' => [
                    'type' => 'object',
                    'additionalProperties' => true,
                ],
            ],
            'additionalProperties' => false,
        ];
    }
}

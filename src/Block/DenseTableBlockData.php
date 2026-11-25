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
 * The `data` of a `dense_table` block: `columns` and `rows`, plus the optional
 * pagination, sort, filters, row and bulk actions, empty state, saved-view
 * control and display `options`. `filters.applied` may be an empty list, the
 * JSON form of an empty PHP array.
 *
 * A schema-only value shape (no instances): it types block data for schema
 * consumers. {@see BlockBuilder} keeps taking plain arrays.
 *
 * @api
 */
class DenseTableBlockData
{
    private function __construct() {}

    /** @return array<string, mixed> */
    public static function jsonSchema(): array
    {
        return [
            'type' => 'object',
            'required' => ['columns', 'rows'],
            'properties' => [
                'columns' => [
                    'type' => 'array',
                    'items' => ['$ref' => '#/$defs/DenseTableColumnDef'],
                ],
                'rows' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'additionalProperties' => true,
                    ],
                ],
                'pagination' => ['$ref' => '#/$defs/DenseTablePagination'],
                'sort' => ['$ref' => '#/$defs/DenseTableSort'],
                'filters' => [
                    'type' => 'object',
                    'required' => ['available', 'applied'],
                    'properties' => [
                        'available' => [
                            'type' => 'array',
                            'items' => ['$ref' => '#/$defs/DenseTableFilterDef'],
                        ],
                        'applied' => [
                            'anyOf' => [
                                [
                                    'type' => 'object',
                                    'additionalProperties' => [
                                        'anyOf' => [
                                            ['type' => 'string'],
                                            [
                                                'type' => 'array',
                                                'items' => ['type' => 'string'],
                                            ],
                                        ],
                                    ],
                                ],
                                [
                                    'type' => 'array',
                                    'maxItems' => 0,
                                ],
                            ],
                        ],
                    ],
                    'additionalProperties' => false,
                ],
                'rowHref' => ['type' => 'string'],
                'rowActions' => [
                    'type' => 'array',
                    'items' => ['$ref' => '#/$defs/ConditionalAction'],
                ],
                'bulkActions' => [
                    'type' => 'array',
                    'items' => ['$ref' => '#/$defs/Action'],
                ],
                'searchPlaceholder' => ['type' => 'string'],
                'emptyState' => ['$ref' => '#/$defs/EmptyStateDef'],
                'saveViewAction' => ['$ref' => '#/$defs/Action'],
                'saveViewActive' => ['type' => 'boolean'],
                'options' => ['$ref' => '#/$defs/TableDisplayOptions'],
            ],
            'additionalProperties' => false,
        ];
    }
}

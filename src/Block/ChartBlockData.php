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
 * The `data` of a `chart` block, as {@see BlockBuilder::chart()} emits it. A
 * data row may be an empty list, the JSON form of an empty PHP array.
 *
 * A schema-only value shape (no instances): it types block data for schema
 * consumers. {@see BlockBuilder} keeps taking plain arrays.
 *
 * @api
 */
final class ChartBlockData
{
    private function __construct() {}

    /** @return array<string, mixed> */
    public static function jsonSchema(): array
    {
        return [
            'type' => 'object',
            'required' => ['chartType', 'categoryKey', 'series', 'data'],
            'properties' => [
                'chartType' => ['$ref' => '#/$defs/ChartType'],
                'categoryKey' => ['type' => 'string'],
                'series' => [
                    'type' => 'array',
                    'items' => ['$ref' => '#/$defs/ChartSeries'],
                ],
                'data' => [
                    'type' => 'array',
                    'items' => [
                        'anyOf' => [
                            [
                                'type' => 'object',
                                'additionalProperties' => true,
                            ],
                            [
                                'type' => 'array',
                                'maxItems' => 0,
                            ],
                        ],
                    ],
                ],
                'options' => [
                    'type' => 'object',
                    'properties' => [
                        'showLegend' => ['type' => 'boolean'],
                        'showGrid' => ['type' => 'boolean'],
                    ],
                    'additionalProperties' => false,
                ],
            ],
            'additionalProperties' => false,
        ];
    }
}

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
 * The `data` of a `chart` block, as {@see BlockBuilder::chart()} emits it.
 *
 * A schema-only value shape (no instances): it types block data for schema
 * consumers. {@see BlockBuilder} keeps taking plain arrays.
 *
 * @api
 */
class ChartBlockData
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
                        'type' => 'object',
                        'additionalProperties' => true,
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

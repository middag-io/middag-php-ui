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
 * One section of a `detail_panel` block.
 *
 * A schema-only value shape (no instances): it types block data for schema
 * consumers. {@see BlockBuilder} keeps taking plain arrays.
 *
 * @api
 */
class DetailPanelSectionDef
{
    private function __construct() {}

    /** @return array<string, mixed> */
    public static function jsonSchema(): array
    {
        return [
            'type' => 'object',
            'required' => ['id', 'title', 'fields'],
            'properties' => [
                'id' => ['type' => 'string'],
                'title' => ['type' => 'string'],
                'fields' => [
                    'type' => 'array',
                    'items' => ['$ref' => '#/$defs/DetailPanelFieldDef'],
                ],
                'defaultCollapsed' => ['type' => 'boolean'],
                'actions' => [
                    'type' => 'array',
                    'items' => ['$ref' => '#/$defs/Action'],
                ],
            ],
            'additionalProperties' => false,
        ];
    }
}

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
 * The `data` of a `status_strip` block: its `items`, with an optional overall
 * `score` and `tone`.
 *
 * A schema-only value shape (no instances): it types block data for schema
 * consumers. {@see BlockBuilder} keeps taking plain arrays.
 *
 * @api
 */
final class StatusStripBlockData
{
    private function __construct() {}

    /** @return array<string, mixed> */
    public static function jsonSchema(): array
    {
        return [
            'type' => 'object',
            'required' => ['items'],
            'properties' => [
                'score' => ['type' => 'number'],
                'tone' => ['$ref' => '#/$defs/StatusStripTone'],
                'items' => [
                    'type' => 'array',
                    'items' => ['$ref' => '#/$defs/StatusStripItem'],
                ],
            ],
            'additionalProperties' => false,
        ];
    }
}

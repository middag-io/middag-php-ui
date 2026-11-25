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
 * The `data` of an `action_grid` block: its action cards and an optional flash
 * message.
 *
 * A schema-only value shape (no instances): it types block data for schema
 * consumers. {@see BlockBuilder} keeps taking plain arrays.
 *
 * @api
 */
class ActionGridBlockData
{
    private function __construct() {}

    /** @return array<string, mixed> */
    public static function jsonSchema(): array
    {
        return [
            'type' => 'object',
            'required' => ['items'],
            'properties' => [
                'items' => [
                    'type' => 'array',
                    'items' => ['$ref' => '#/$defs/ActionGridItem'],
                ],
                'flash' => [
                    'anyOf' => [
                        [
                            'type' => 'object',
                            'required' => ['success', 'message'],
                            'properties' => [
                                'success' => ['type' => 'boolean'],
                                'message' => ['type' => 'string'],
                            ],
                            'additionalProperties' => false,
                        ],
                        ['type' => 'null'],
                    ],
                ],
            ],
            'additionalProperties' => false,
        ];
    }
}

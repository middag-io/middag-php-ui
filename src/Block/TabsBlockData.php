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
 * The `data` of a `tabs` block, as {@see BlockBuilder::tabs()} emits it.
 *
 * A schema-only value shape (no instances): it types block data for schema
 * consumers. {@see BlockBuilder} keeps taking plain arrays.
 *
 * @api
 */
final class TabsBlockData
{
    private function __construct() {}

    /** @return array<string, mixed> */
    public static function jsonSchema(): array
    {
        return [
            'type' => 'object',
            'required' => ['tabs'],
            'properties' => [
                'tabs' => [
                    'type' => 'array',
                    'items' => ['$ref' => '#/$defs/Tab'],
                ],
                'defaultTab' => ['type' => 'string'],
            ],
            'additionalProperties' => false,
        ];
    }
}

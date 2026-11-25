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
 * The empty state a data block shows when it has no rows: an optional icon,
 * title, description and call to action.
 *
 * A schema-only value shape (no instances), shared by the block data shapes
 * that carry an `emptyState` key.
 *
 * @api
 */
final class EmptyStateDef
{
    private function __construct() {}

    /** @return array<string, mixed> */
    public static function jsonSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'icon' => ['type' => 'string'],
                'title' => ['type' => 'string'],
                'description' => ['type' => 'string'],
                'cta' => [
                    'type' => 'object',
                    'required' => ['label', 'href'],
                    'properties' => [
                        'label' => ['type' => 'string'],
                        'href' => ['type' => 'string'],
                    ],
                    'additionalProperties' => false,
                ],
            ],
            'additionalProperties' => false,
        ];
    }
}

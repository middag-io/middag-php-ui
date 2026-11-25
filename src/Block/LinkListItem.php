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
 * One row of a `link_list` block. `trailing` is short text pinned to the
 * right; `active` marks the current selection.
 *
 * A schema-only value shape (no instances): it types block data for schema
 * consumers. {@see BlockBuilder} keeps taking plain arrays.
 *
 * @api
 */
class LinkListItem
{
    private function __construct() {}

    /** @return array<string, mixed> */
    public static function jsonSchema(): array
    {
        return [
            'type' => 'object',
            'required' => ['label', 'href'],
            'properties' => [
                'label' => ['type' => 'string'],
                'href' => [
                    'type' => ['string', 'null'],
                ],
                'icon' => ['type' => 'string'],
                'description' => ['type' => 'string'],
                'external' => ['type' => 'boolean'],
                'trailing' => ['type' => 'string'],
                'active' => ['type' => 'boolean'],
            ],
            'additionalProperties' => false,
        ];
    }
}

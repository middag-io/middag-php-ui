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
 * One link of a `link_group` cell value.
 *
 * A schema-only value shape (no instances): it types block data for schema
 * consumers. {@see BlockBuilder} keeps taking plain arrays.
 *
 * @api
 */
final class LinkGroupItem
{
    private function __construct() {}

    /** @return array<string, mixed> */
    public static function jsonSchema(): array
    {
        return [
            'type' => 'object',
            'required' => ['icon', 'href', 'label'],
            'properties' => [
                'icon' => ['type' => 'string'],
                'href' => [
                    'type' => ['string', 'null'],
                ],
                'label' => ['type' => 'string'],
                'external' => ['type' => 'boolean'],
            ],
            'additionalProperties' => false,
        ];
    }
}

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
 * A reference to a navigable entity, resolved by the client through the page
 * `entities` map: the entity `type` key and the row field (`id`) that holds
 * its id.
 *
 * A schema-only value shape (no instances): it types block data for schema
 * consumers. {@see BlockBuilder} keeps taking plain arrays.
 *
 * @api
 */
final class EntityRef
{
    private function __construct() {}

    /** @return array<string, mixed> */
    public static function jsonSchema(): array
    {
        return [
            'type' => 'object',
            'required' => ['type', 'id'],
            'properties' => [
                'type' => ['type' => 'string'],
                'id' => ['type' => 'string'],
            ],
            'additionalProperties' => false,
        ];
    }
}

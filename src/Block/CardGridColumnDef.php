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
 * One field shown on each card of a `card_grid` block: the row `key` it reads,
 * its `label`, and an optional display `kind`.
 *
 * A schema-only value shape (no instances): it types the `columns` of
 * {@see CardGridBlockData}. {@see BlockBuilder::cardGrid()} keeps taking plain arrays.
 *
 * @api
 */
class CardGridColumnDef
{
    private function __construct() {}

    /** @return array<string, mixed> */
    public static function jsonSchema(): array
    {
        return [
            'type' => 'object',
            'required' => ['key', 'label'],
            'properties' => [
                'key' => ['type' => 'string'],
                'label' => ['type' => 'string'],
                'kind' => ['enum' => ['text', 'status', 'badge', 'boolean', 'timestamp', 'number']],
            ],
            'additionalProperties' => false,
        ];
    }
}

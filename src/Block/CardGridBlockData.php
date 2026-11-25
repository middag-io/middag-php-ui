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
 * The `data` of a `card_grid` block: the `columns` each card shows, the
 * `rows` (one card each), and the optional `variant`, `emptyState`,
 * `editHref` (a per-card edit link pattern, interpolated per row) and
 * `rowActions`.
 *
 * A schema-only value shape (no instances): it types the block payload for
 * schema consumers. It is not referenced from {@see BlockDescriptor}, whose
 * `data` stays an open object, and {@see BlockBuilder::cardGrid()} keeps
 * taking plain arrays.
 *
 * @api
 */
final class CardGridBlockData
{
    private function __construct() {}

    /** @return array<string, mixed> */
    public static function jsonSchema(): array
    {
        return [
            'type' => 'object',
            'required' => ['columns', 'rows'],
            'properties' => [
                'columns' => ['type' => 'array', 'items' => ['$ref' => '#/$defs/CardGridColumnDef']],
                'rows' => ['type' => 'array', 'items' => ['type' => 'object', 'additionalProperties' => true]],
                'variant' => ['$ref' => '#/$defs/CardGridVariant'],
                'emptyState' => ['$ref' => '#/$defs/EmptyStateDef'],
                'editHref' => ['type' => 'string'],
                'rowActions' => ['type' => 'array', 'items' => ['$ref' => '#/$defs/ConditionalAction']],
            ],
            'additionalProperties' => false,
        ];
    }
}

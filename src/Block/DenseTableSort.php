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
 * Sort state of a `dense_table` block: the sorted column and direction, both
 * null when unsorted.
 *
 * A schema-only value shape (no instances): it types block data for schema
 * consumers. {@see BlockBuilder} keeps taking plain arrays.
 *
 * @api
 */
final class DenseTableSort
{
    private function __construct() {}

    /** @return array<string, mixed> */
    public static function jsonSchema(): array
    {
        return [
            'type' => 'object',
            'required' => ['column', 'direction'],
            'properties' => [
                'column' => [
                    'type' => ['string', 'null'],
                ],
                'direction' => [
                    'enum' => ['asc', 'desc', null],
                ],
            ],
            'additionalProperties' => false,
        ];
    }
}

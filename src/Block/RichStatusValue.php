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
 * Cell value of a `rich_status` column: a label with a semantic appearance and
 * an optional icon.
 *
 * A schema-only value shape (no instances): it types block data for schema
 * consumers. {@see BlockBuilder} keeps taking plain arrays.
 *
 * @api
 */
class RichStatusValue
{
    private function __construct() {}

    /** @return array<string, mixed> */
    public static function jsonSchema(): array
    {
        return [
            'type' => 'object',
            'required' => ['label', 'appearance'],
            'properties' => [
                'label' => ['type' => 'string'],
                'appearance' => [
                    'enum' => ['success', 'warning', 'danger', 'info', 'neutral'],
                ],
                'icon' => ['type' => 'string'],
            ],
            'additionalProperties' => false,
        ];
    }
}

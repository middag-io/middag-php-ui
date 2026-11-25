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
 * One item of a `status_strip` block.
 *
 * A schema-only value shape (no instances): it types block data for schema
 * consumers. {@see BlockBuilder} keeps taking plain arrays.
 *
 * @api
 */
final class StatusStripItem
{
    private function __construct() {}

    /** @return array<string, mixed> */
    public static function jsonSchema(): array
    {
        return [
            'type' => 'object',
            'required' => ['key', 'label', 'value', 'appearance'],
            'properties' => [
                'key' => ['type' => 'string'],
                'label' => ['type' => 'string'],
                'value' => ['type' => 'string'],
                'appearance' => [
                    'enum' => ['success', 'warning', 'danger', 'info', 'neutral'],
                ],
                'tooltip' => ['type' => 'string'],
                'href' => ['type' => 'string'],
            ],
            'additionalProperties' => false,
        ];
    }
}

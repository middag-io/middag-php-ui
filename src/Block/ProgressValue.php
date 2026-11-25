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
 * Cell value of a `progress` column: the current value, an optional maximum
 * (100 by default), label and appearance.
 *
 * A schema-only value shape (no instances): it types block data for schema
 * consumers. {@see BlockBuilder} keeps taking plain arrays.
 *
 * @api
 */
class ProgressValue
{
    private function __construct() {}

    /** @return array<string, mixed> */
    public static function jsonSchema(): array
    {
        return [
            'type' => 'object',
            'required' => ['value'],
            'properties' => [
                'value' => ['type' => 'number'],
                'max' => ['type' => 'number'],
                'label' => ['type' => 'string'],
                'appearance' => [
                    'enum' => ['success', 'warning', 'danger', 'info', 'neutral'],
                ],
            ],
            'additionalProperties' => false,
        ];
    }
}

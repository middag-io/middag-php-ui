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
 * Cell value of an `annotated` column: primary text with an optional prefix
 * badge, muted sublabel and appearance.
 *
 * A schema-only value shape (no instances): it types block data for schema
 * consumers. {@see BlockBuilder} keeps taking plain arrays.
 *
 * @api
 */
class AnnotatedValue
{
    private function __construct() {}

    /** @return array<string, mixed> */
    public static function jsonSchema(): array
    {
        return [
            'type' => 'object',
            'required' => ['text'],
            'properties' => [
                'text' => ['type' => 'string'],
                'badge' => [
                    'type' => 'object',
                    'required' => ['label', 'color'],
                    'properties' => [
                        'label' => ['type' => 'string'],
                        'color' => ['type' => 'string'],
                    ],
                    'additionalProperties' => false,
                ],
                'sublabel' => ['type' => 'string'],
                'appearance' => [
                    'enum' => ['success', 'warning', 'danger', 'info', 'neutral'],
                ],
            ],
            'additionalProperties' => false,
        ];
    }
}

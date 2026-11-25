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
 * The `data` of an `empty_state` block: a variant, icon or animation,
 * description and up to two calls to action.
 *
 * A schema-only value shape (no instances): it types block data for schema
 * consumers. {@see BlockBuilder} keeps taking plain arrays.
 *
 * @api
 */
final class EmptyStateBlockData
{
    private function __construct() {}

    /** @return array<string, mixed> */
    public static function jsonSchema(): array
    {
        return [
            'type' => 'object',
            'required' => ['description'],
            'properties' => [
                'variant' => [
                    'enum' => ['first-use', 'no-results', 'error', 'permission'],
                ],
                'icon' => ['type' => 'string'],
                'lottieUrl' => ['type' => 'string'],
                'description' => ['type' => 'string'],
                'cta' => [
                    'type' => 'object',
                    'required' => ['label'],
                    'properties' => [
                        'label' => ['type' => 'string'],
                        'href' => ['type' => 'string'],
                        'method' => [
                            'enum' => ['get', 'post'],
                        ],
                    ],
                    'additionalProperties' => false,
                ],
                'ctaSecondary' => [
                    'type' => 'object',
                    'required' => ['label', 'href'],
                    'properties' => [
                        'label' => ['type' => 'string'],
                        'href' => ['type' => 'string'],
                    ],
                    'additionalProperties' => false,
                ],
            ],
            'additionalProperties' => false,
        ];
    }
}

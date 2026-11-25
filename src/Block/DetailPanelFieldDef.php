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
 * One field of a `detail_panel` section. `entity.id` is the entity id value
 * itself, not a row field name.
 *
 * A schema-only value shape (no instances): it types block data for schema
 * consumers. {@see BlockBuilder} keeps taking plain arrays.
 *
 * @api
 */
final class DetailPanelFieldDef
{
    private function __construct() {}

    /** @return array<string, mixed> */
    public static function jsonSchema(): array
    {
        return [
            'type' => 'object',
            'required' => ['key', 'label', 'value'],
            'properties' => [
                'key' => ['type' => 'string'],
                'label' => ['type' => 'string'],
                'value' => [
                    'type' => ['string', 'number', 'boolean', 'null'],
                ],
                'kind' => [
                    'enum' => ['text', 'status', 'timestamp', 'boolean', 'link', 'code', 'email'],
                ],
                'copyable' => ['type' => 'boolean'],
                'href' => ['type' => 'string'],
                'entity' => [
                    'type' => 'object',
                    'required' => ['type', 'id'],
                    'properties' => [
                        'type' => ['type' => 'string'],
                        'id' => [
                            'type' => ['string', 'number'],
                        ],
                    ],
                    'additionalProperties' => false,
                ],
            ],
            'additionalProperties' => false,
        ];
    }
}

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
 * The `data` of an `activity_timeline` block: labelled `groups` of entries,
 * with optional load-more paging and a `limit`.
 *
 * A schema-only value shape (no instances): it types block data for schema
 * consumers. {@see BlockBuilder} keeps taking plain arrays.
 *
 * @api
 */
final class ActivityTimelineBlockData
{
    private function __construct() {}

    /** @return array<string, mixed> */
    public static function jsonSchema(): array
    {
        return [
            'type' => 'object',
            'required' => ['groups'],
            'properties' => [
                'groups' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'required' => ['label', 'entries'],
                        'properties' => [
                            'label' => ['type' => 'string'],
                            'entries' => [
                                'type' => 'array',
                                'items' => ['$ref' => '#/$defs/ActivityTimelineEntry'],
                            ],
                        ],
                        'additionalProperties' => false,
                    ],
                ],
                'hasMore' => ['type' => 'boolean'],
                'loadMoreHref' => ['type' => 'string'],
                'limit' => ['type' => 'integer'],
            ],
            'additionalProperties' => false,
        ];
    }
}

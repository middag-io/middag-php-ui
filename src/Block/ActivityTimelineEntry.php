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
 * One entry of an `activity_timeline` block. `read` and `markReadHref` drive
 * the optional mark-as-read control.
 *
 * A schema-only value shape (no instances): it types block data for schema
 * consumers. {@see BlockBuilder} keeps taking plain arrays.
 *
 * @api
 */
class ActivityTimelineEntry
{
    private function __construct() {}

    /** @return array<string, mixed> */
    public static function jsonSchema(): array
    {
        return [
            'type' => 'object',
            'required' => ['id', 'actor', 'action', 'icon', 'color', 'timestamp'],
            'properties' => [
                'id' => ['type' => 'string'],
                'actor' => ['type' => 'string'],
                'actorHref' => ['type' => 'string'],
                'action' => ['type' => 'string'],
                'detail' => ['type' => 'string'],
                'icon' => ['type' => 'string'],
                'color' => [
                    'enum' => ['success', 'info', 'warning', 'destructive', 'neutral'],
                ],
                'timestamp' => ['type' => 'number'],
                'read' => ['type' => 'boolean'],
                'markReadHref' => ['type' => 'string'],
            ],
            'additionalProperties' => false,
        ];
    }
}

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

use Middag\Ui\Action\Action;

/**
 * One card of an `action_grid` block: the canonical {@see Action} plus the
 * card's `title` and `description`.
 *
 * A schema-only value shape (no instances): it types block data for schema
 * consumers. {@see BlockBuilder} keeps taking plain arrays.
 *
 * @api
 */
final class ActionGridItem
{
    private function __construct() {}

    /** @return array<string, mixed> */
    public static function jsonSchema(): array
    {
        $schema = Action::jsonSchema();
        $schema['required'] = [...$schema['required'], 'title', 'description'];
        $schema['properties'] = [
            ...$schema['properties'],
            'title' => ['type' => 'string'],
            'description' => ['type' => 'string'],
        ];

        return $schema;
    }
}

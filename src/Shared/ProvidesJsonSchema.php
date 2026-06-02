<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Shared;

/**
 * Emits the JSON Schema fragment for a string-backed enum.
 *
 * The schema is derived from {@see self::cases()} so the enum cases are the
 * single source of truth — the schema can never drift from the wire values.
 * Used by the schema emitter (bin/emit-schemas.php) to bundle every enum into
 * the wire-contract `$defs`.
 *
 * @internal
 */
trait ProvidesJsonSchema
{
    /**
     * @return array<string, mixed>
     */
    public static function jsonSchema(): array
    {
        return [
            'type' => 'string',
            'enum' => array_map(static fn (self $case): string => $case->value, self::cases()),
        ];
    }
}

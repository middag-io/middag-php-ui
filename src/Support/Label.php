<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Support;

use Middag\Ui\Data\Translatable;

/**
 * Stateless helper to serialize a UI label that may be either an i18n intent
 * ({@see Translatable}) or a raw literal string.
 *
 * A Translatable serializes to its `{key, domain, params?}` payload; a string
 * passes through unchanged. The client distinguishes by type at render time.
 *
 * @api
 */
final class Label
{
    /**
     * @return array<string, mixed>|string
     */
    public static function serialize(string|Translatable $label): array|string
    {
        return $label instanceof Translatable ? $label->jsonSerialize() : $label;
    }

    /**
     * @return null|array<string, mixed>|string
     */
    public static function serializeNullable(string|Translatable|null $label): array|string|null
    {
        return $label === null ? null : self::serialize($label);
    }
}

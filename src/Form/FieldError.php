<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Form;

/**
 * The error value for a single form field: one message, a list of messages or
 * structured messages, or one structured message ({@see FieldMessage}).
 *
 * A schema-only value shape (no instances) — it is referenced by
 * {@see FormErrors} as the value type of the error map.
 *
 * @api
 */
final class FieldError
{
    private function __construct() {}

    /** @return array<string, mixed> */
    public static function jsonSchema(): array
    {
        return [
            'oneOf' => [
                ['type' => 'string'],
                ['type' => 'array', 'items' => ['oneOf' => [['type' => 'string'], ['$ref' => '#/$defs/FieldMessage']]]],
                ['$ref' => '#/$defs/FieldMessage'],
            ],
        ];
    }
}

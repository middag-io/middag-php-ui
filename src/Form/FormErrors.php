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

use JsonSerializable;

/**
 * Validation errors for a form submission, keyed by field name.
 *
 * Each value is a {@see FieldError} (one message or a list). Two reserved key
 * conventions the client honors:
 *  - The form-level key `_` ({@see self::FORM_LEVEL_KEY}) carries errors that
 *    belong to the whole form rather than a single field; the client renders
 *    these as a form-level alert instead of routing them to an input.
 *  - Dotted keys (e.g. `address.zip`) target nested fields.
 *
 * @api
 */
final readonly class FormErrors implements JsonSerializable
{
    /** Reserved key for errors that apply to the whole form, not one field. */
    public const FORM_LEVEL_KEY = '_';

    /**
     * @param array<string, string|string[]> $errors field name → message(s)
     */
    public function __construct(
        public array $errors = [],
    ) {}

    /** @return array<string, string|string[]> */
    public function jsonSerialize(): array
    {
        return $this->errors;
    }

    /** @return array<string, mixed> */
    public static function jsonSchema(): array
    {
        return [
            'type' => 'object',
            'description' => 'Validation errors keyed by field name. The reserved key "_" carries form-level errors; dotted keys target nested fields.',
            'additionalProperties' => ['$ref' => '#/$defs/FieldError'],
        ];
    }
}

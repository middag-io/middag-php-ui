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
use stdClass;

/**
 * Validation errors for a form submission, keyed by field name.
 *
 * Each value is a {@see FieldError}: one message, a list of messages or
 * structured messages, or one structured message ({@see FieldMessage}). A
 * structured message may also be given as its serialized array (for example
 * when the map is rebuilt from the session); it passes through unchanged.
 * Two reserved key conventions the client honors:
 *  - The form-level key `_` ({@see self::FORM_LEVEL_KEY}) carries errors that
 *    belong to the whole form rather than a single field; the client renders
 *    these as a form-level alert instead of routing them to an input.
 *  - Dotted keys (e.g. `address.zip`) target nested fields.
 *
 * An empty set serializes to `{}`, never `[]`.
 *
 * @api
 */
final readonly class FormErrors implements JsonSerializable
{
    /** Reserved key for errors that apply to the whole form, not one field. */
    public const string FORM_LEVEL_KEY = '_';

    /**
     * @param array<string, array{message: string, key: string, domain: string, params?: array<string, mixed>}|FieldMessage|list<array{message: string, key: string, domain: string, params?: array<string, mixed>}|FieldMessage|string>|string> $errors field name → message(s)
     */
    public function __construct(
        public array $errors = [],
    ) {}

    /**
     * Every {@see FieldMessage}, alone or inside a list, becomes its array form.
     *
     * @return array<string, mixed>|stdClass
     */
    public function jsonSerialize(): array|stdClass
    {
        if ($this->errors === []) {
            return new stdClass();
        }

        return array_map($this->normalize(...), $this->errors);
    }

    /** @return array<string, mixed> */
    public static function jsonSchema(): array
    {
        return [
            'type' => 'object',
            'description' => 'Validation errors keyed by field name. Each value is a message, a structured message (FieldMessage) or a list of either. The reserved key "_" carries form-level errors; dotted keys target nested fields.',
            'additionalProperties' => ['$ref' => '#/$defs/FieldError'],
        ];
    }

    private function normalize(mixed $value): mixed
    {
        if ($value instanceof FieldMessage) {
            return $value->jsonSerialize();
        }

        if (is_array($value) && array_is_list($value)) {
            return array_map(
                static fn (mixed $item): mixed => $item instanceof FieldMessage ? $item->jsonSerialize() : $item,
                $value,
            );
        }

        return $value;
    }
}

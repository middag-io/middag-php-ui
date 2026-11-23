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

use InvalidArgumentException;
use JsonSerializable;
use stdClass;

/**
 * Validation errors for a form submission, keyed by field name.
 *
 * Each value is a {@see FieldError}: one message, a list of messages or
 * structured messages, or one structured message ({@see FieldMessage}). A
 * structured message may also be given as its serialized array (for example
 * when the map is rebuilt from the session); it is rebuilt as a FieldMessage on
 * serialization, so an empty `params` is dropped and a list `params` is
 * rejected, exactly as for a FieldMessage instance.
 * Two reserved key conventions the client honors:
 *  - The form-level key `_` ({@see self::FORM_LEVEL_KEY}) carries errors that
 *    belong to the whole form rather than a single field; the client renders
 *    these as a form-level alert instead of routing them to an input.
 *  - Dotted keys (e.g. `address.zip`) target nested fields.
 *
 * The map always serializes as a JSON object: an empty set is `{}`, never
 * `[]`, and sequential numeric field names (`0`, `1`) stay object keys.
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
     * Every structured message ({@see FieldMessage} or its array form), alone
     * or inside a list, becomes its normalized array form. The result is always
     * an object, so numeric field names do not collapse into a JSON list.
     *
     * @return stdClass
     *
     * @throws InvalidArgumentException when a structured message array lacks message/key/domain or carries list params
     */
    public function jsonSerialize(): stdClass
    {
        return (object) array_map($this->normalize(...), $this->errors);
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
        if (is_array($value) && array_is_list($value)) {
            return array_map($this->normalizeMessage(...), $value);
        }

        return $this->normalizeMessage($value);
    }

    /** One message: a string passes through; a structured message is rebuilt as a FieldMessage. */
    private function normalizeMessage(mixed $value): mixed
    {
        if ($value instanceof FieldMessage) {
            return $value->jsonSerialize();
        }

        if (is_array($value)) {
            return $this->fieldMessageFromArray($value)->jsonSerialize();
        }

        return $value;
    }

    /** @param array<array-key, mixed> $value */
    private function fieldMessageFromArray(array $value): FieldMessage
    {
        $message = $value['message'] ?? null;
        $key = $value['key'] ?? null;
        $domain = $value['domain'] ?? null;
        $params = $value['params'] ?? [];

        if (!is_string($message) || !is_string($key) || !is_string($domain) || !is_array($params)) {
            throw new InvalidArgumentException('A structured field message needs string message, key and domain, and array params.');
        }

        return new FieldMessage($message, $key, $domain, $params);
    }
}

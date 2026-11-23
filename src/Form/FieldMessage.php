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

/**
 * A structured validation message for one form field.
 *
 * Carries the message the server already resolved (`message`, the fallback
 * the client prints as is) next to the translation key, the host i18n
 * namespace (`domain`) and the placeholder values, so a client that owns the
 * translations can render the message in the user's language.
 *
 * Wire: `{message, key, domain, params?}`; `params` is omitted when empty.
 *
 * @api
 */
final readonly class FieldMessage implements JsonSerializable
{
    /**
     * @param string               $message Server-resolved message, used as the fallback
     * @param string               $key     Translation key
     * @param string               $domain  Host i18n namespace (the host's translation grouping)
     * @param array<string, mixed> $params  Placeholder values for interpolation, keyed by name
     *
     * @throws InvalidArgumentException when $params is a non-empty list (it would not encode as a JSON object)
     */
    public function __construct(
        public string $message,
        public string $key,
        public string $domain,
        public array $params = [],
    ) {
        $this->assertParamsMap($params);
    }

    /** @return array{message: string, key: string, domain: string, params?: array<string, mixed>} */
    public function jsonSerialize(): array
    {
        $payload = [
            'message' => $this->message,
            'key' => $this->key,
            'domain' => $this->domain,
        ];

        if ($this->params !== []) {
            $payload['params'] = $this->params;
        }

        return $payload;
    }

    /** @return array<string, mixed> */
    public static function jsonSchema(): array
    {
        return [
            'type' => 'object',
            'description' => 'A structured validation message: the server-resolved message plus the translation key, domain and optional params.',
            'required' => ['message', 'key', 'domain'],
            'properties' => [
                'message' => ['type' => 'string'],
                'key' => ['type' => 'string'],
                'domain' => ['type' => 'string'],
                'params' => ['type' => 'object', 'additionalProperties' => true],
            ],
            'additionalProperties' => false,
        ];
    }

    /**
     * A non-empty list encodes as a JSON array, which the schema rejects.
     *
     * @param array<array-key, mixed> $params
     */
    private function assertParamsMap(array $params): void
    {
        if ($params !== [] && array_is_list($params)) {
            throw new InvalidArgumentException('FieldMessage params must be keyed by placeholder name, not a list.');
        }
    }
}

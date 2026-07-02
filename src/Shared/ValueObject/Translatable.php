<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Shared\ValueObject;

use JsonSerializable;

/**
 * Structured i18n intent for a UI label/title/message.
 *
 * The library never resolves translations. It carries a translation key plus
 * the host i18n namespace (`domain`); the client resolves it with the host's
 * own i18n tool, reading user preferences. Entity data (names, numbers) stays
 * a raw `string`, never a Translatable — the client distinguishes by type
 * (object → resolve, string → print literally).
 *
 * @api
 */
final readonly class Translatable implements JsonSerializable
{
    /**
     * @param string               $key    Translation key
     * @param string               $domain Host i18n namespace (the host's translation grouping)
     * @param array<string, mixed> $params Placeholder values for interpolation
     */
    public function __construct(
        public string $key,
        public string $domain,
        public array $params = [],
    ) {}

    /**
     * @param array<string, mixed> $params
     */
    public static function of(string $key, string $domain, array $params = []): self
    {
        return new self($key, $domain, $params);
    }

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        $payload = [
            'key' => $this->key,
            'domain' => $this->domain,
        ];

        if ($this->params !== []) {
            $payload['params'] = $this->params;
        }

        return $payload;
    }

    /**
     * JSON Schema for the Translatable object — the structured branch of every
     * Label-union field on the wire ({@see Label}).
     *
     * @return array<string, mixed>
     */
    public static function jsonSchema(): array
    {
        return [
            'type' => 'object',
            'required' => ['key', 'domain'],
            'properties' => [
                'key' => ['type' => 'string'],
                'domain' => ['type' => 'string'],
                // params: free-form interpolation map, omit-empty.
                'params' => ['type' => 'object', 'additionalProperties' => true],
            ],
            'additionalProperties' => false,
        ];
    }
}

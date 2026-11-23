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
use stdClass;

/**
 * One-shot flash messages for the next render: a flat map with at most one
 * string per level (`success`, `error`, `info`, `warning`).
 *
 * Only the keys that are set are emitted; an empty set serializes to `{}`.
 *
 * @api
 */
final readonly class FlashMessages implements JsonSerializable
{
    /** The only keys the wire accepts. */
    public const array KEYS = ['success', 'error', 'info', 'warning'];

    public function __construct(
        public ?string $success = null,
        public ?string $error = null,
        public ?string $info = null,
        public ?string $warning = null,
    ) {}

    /**
     * Build from a raw flash map (for example a session flash bag). Keys outside
     * {@see self::KEYS} and non-string values are discarded, not rejected.
     *
     * @param array<array-key, mixed> $raw
     */
    public static function fromArray(array $raw): self
    {
        $values = [];
        foreach (self::KEYS as $key) {
            $values[$key] = isset($raw[$key]) && is_string($raw[$key]) ? $raw[$key] : null;
        }

        return new self(
            success: $values['success'],
            error: $values['error'],
            info: $values['info'],
            warning: $values['warning'],
        );
    }

    /** @return array<string, string>|stdClass */
    public function jsonSerialize(): array|stdClass
    {
        $payload = array_filter([
            'success' => $this->success,
            'error' => $this->error,
            'info' => $this->info,
            'warning' => $this->warning,
        ], static fn (?string $message): bool => $message !== null);

        return $payload === [] ? new stdClass() : $payload;
    }

    /** @return array<string, mixed> */
    public static function jsonSchema(): array
    {
        return ['type' => 'object',
            'properties' => [
                'success' => ['type' => 'string'],
                'error' => ['type' => 'string'],
                'info' => ['type' => 'string'],
                'warning' => ['type' => 'string'],
            ],
            'additionalProperties' => false];
    }
}

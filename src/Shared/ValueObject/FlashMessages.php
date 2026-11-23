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
 * Only the keys that carry text are emitted: null, empty and whitespace-only
 * values are discarded. An empty set serializes to `{}`.
 *
 * @api
 */
final readonly class FlashMessages implements JsonSerializable
{
    /** The only keys the wire accepts. */
    public const array KEYS = ['success', 'error', 'info', 'warning'];

    public ?string $success;

    public ?string $error;

    public ?string $info;

    public ?string $warning;

    public function __construct(
        ?string $success = null,
        ?string $error = null,
        ?string $info = null,
        ?string $warning = null,
    ) {
        $this->success = $this->normalize($success);
        $this->error = $this->normalize($error);
        $this->info = $this->normalize($info);
        $this->warning = $this->normalize($warning);
    }

    /**
     * Build from a raw flash map (for example a session flash bag). Keys outside
     * {@see self::KEYS}, non-string values and empty or whitespace-only strings
     * are discarded, not rejected.
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

    private function normalize(?string $message): ?string
    {
        return $message === null || trim($message) === '' ? null : $message;
    }
}

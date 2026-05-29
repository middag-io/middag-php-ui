<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Region;

use JsonSerializable;

/**
 * Polling / live-refresh configuration for a block or inspector.
 *
 * Covers near-real-time refresh. SSE / websocket transport is out of MVP
 * scope.
 *
 * @api
 */
final readonly class PollConfig implements JsonSerializable
{
    public function __construct(
        public string $endpoint,
        public int $intervalMs,
        public ?int $stopAfterMs = null,
        public bool $pauseWhenHidden = true,
    ) {}

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        $payload = [
            'endpoint' => $this->endpoint,
            'intervalMs' => $this->intervalMs,
            'pauseWhenHidden' => $this->pauseWhenHidden,
        ];

        if ($this->stopAfterMs !== null) {
            $payload['stopAfterMs'] = $this->stopAfterMs;
        }

        return $payload;
    }

    /** @return array<string, mixed> */
    public static function jsonSchema(): array
    {
        return ['type' => 'object', 'required' => ['endpoint', 'intervalMs', 'pauseWhenHidden'],
            'properties' => ['endpoint' => ['type' => 'string'], 'intervalMs' => ['type' => 'integer'], 'pauseWhenHidden' => ['type' => 'boolean'], 'stopAfterMs' => ['type' => 'integer']],
            'additionalProperties' => false];
    }
}

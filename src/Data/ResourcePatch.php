<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Data;

use JsonSerializable;

/**
 * Partial update to the shared page resources.
 *
 * Lets the server push a change to preferences, capabilities, or feature flags
 * alongside a Fragment or ActionResult, without resending the whole
 * PageResources envelope. Every field is optional; the wire payload omits
 * what is unset, and the keys mirror PageResources so the client can merge.
 *
 * @api
 */
final readonly class ResourcePatch implements JsonSerializable
{
    /**
     * @param array<string, bool> $capabilities
     * @param array<string, bool> $feature_flags
     */
    public function __construct(
        public ?UserPreferences $preferences = null,
        public array $capabilities = [],
        public array $feature_flags = [],
    ) {}

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        $payload = [];

        if ($this->preferences instanceof UserPreferences) {
            $payload['preferences'] = $this->preferences->jsonSerialize();
        }

        if ($this->capabilities !== []) {
            $payload['capabilities'] = $this->capabilities;
        }

        if ($this->feature_flags !== []) {
            $payload['featureFlags'] = $this->feature_flags;
        }

        return $payload;
    }
}

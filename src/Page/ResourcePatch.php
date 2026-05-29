<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Page;

use JsonSerializable;
use Middag\Ui\Action\ActionResult;
use Middag\Ui\Region\Fragment;
use Middag\Ui\Shared\Data\UserPreferences;

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
     * @param array<string, bool> $featureFlags
     */
    public function __construct(
        public ?UserPreferences $preferences = null,
        public array $capabilities = [],
        public array $featureFlags = [],
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

        if ($this->featureFlags !== []) {
            $payload['featureFlags'] = $this->featureFlags;
        }

        return $payload;
    }

    /** @return array<string, mixed> */
    public static function jsonSchema(): array
    {
        return [
            'oneOf' => [
                [
                    'type' => 'object',
                    'properties' => [
                        'preferences' => ['$ref' => '#/$defs/UserPreferences'],
                        'capabilities' => ['type' => 'object', 'additionalProperties' => ['type' => 'boolean']],
                        'featureFlags' => ['type' => 'object', 'additionalProperties' => ['type' => 'boolean']],
                    ],
                    'additionalProperties' => false,
                ],
                // An empty patch serializes to an empty PHP array → JSON [].
                ['type' => 'array', 'maxItems' => 0],
            ],
        ];
    }
}

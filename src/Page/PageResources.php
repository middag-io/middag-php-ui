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

use Middag\Ui\Shared\Data\Identity;
use Middag\Ui\Shared\Data\UserPreferences;

/**
 * Shared page resources: user preferences, capabilities, feature flags,
 * identity, and branding.
 *
 * @api
 */
readonly class PageResources implements PageResourcesInterface
{
    /**
     * @param array<string, bool> $capabilities
     * @param array<string, bool> $featureFlags
     */
    public function __construct(
        public UserPreferences $preferences = new UserPreferences(),
        public array $capabilities = [],
        public array $featureFlags = [],
        public ?Identity $user = null,
        public ?Branding $branding = null,
    ) {}

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        $payload = [
            'preferences' => $this->preferences->jsonSerialize(),
            'capabilities' => $this->capabilities,
            'featureFlags' => $this->featureFlags,
        ];

        if ($this->user instanceof Identity) {
            $payload['user'] = $this->user->jsonSerialize();
        }

        if ($this->branding instanceof Branding) {
            $payload['branding'] = $this->branding->jsonSerialize();
        }

        return $payload;
    }

    /** @return array<string, mixed> */
    public static function jsonSchema(): array
    {
        return ['type' => 'object', 'required' => ['preferences', 'capabilities', 'featureFlags'],
            'properties' => ['preferences' => ['$ref' => '#/$defs/UserPreferences'],
                'capabilities' => ['oneOf' => [['type' => 'object', 'additionalProperties' => ['type' => 'boolean']], ['type' => 'array', 'maxItems' => 0]]],
                'featureFlags' => ['oneOf' => [['type' => 'object', 'additionalProperties' => ['type' => 'boolean']], ['type' => 'array', 'maxItems' => 0]]],
                'user' => ['$ref' => '#/$defs/Identity'], 'branding' => ['$ref' => '#/$defs/Branding']],
            'additionalProperties' => false];
    }
}

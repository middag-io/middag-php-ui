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

use Middag\Ui\Contract\PageResourcesInterface;

readonly class PageResources implements PageResourcesInterface
{
    /**
     * @param array<string, bool> $capabilities
     * @param array<string, bool> $feature_flags
     */
    public function __construct(
        public UserPreferences $preferences = new UserPreferences(),
        public array $capabilities = [],
        public array $feature_flags = [],
        public ?Identity $user = null,
        public ?Branding $branding = null,
    ) {}

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        $payload = [
            'preferences' => $this->preferences->jsonSerialize(),
            'capabilities' => $this->capabilities,
            'featureFlags' => $this->feature_flags,
        ];

        if ($this->user instanceof Identity) {
            $payload['user'] = $this->user->jsonSerialize();
        }

        if ($this->branding instanceof Branding) {
            $payload['branding'] = $this->branding->jsonSerialize();
        }

        return $payload;
    }
}

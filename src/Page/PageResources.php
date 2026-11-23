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

use Middag\Ui\Page\Contract\PageResourcesInterface;
use Middag\Ui\Shared\ValueObject\SharedProps;
use stdClass;

/**
 * Shared page resources: feature flags and branding.
 *
 * Identity, capabilities, theme and locale travel in the shared props
 * ({@see SharedProps}), not here.
 *
 * @api
 */
readonly class PageResources implements PageResourcesInterface
{
    /**
     * @param array<string, bool> $featureFlags
     */
    public function __construct(
        public array $featureFlags = [],
        public ?Branding $branding = null,
    ) {}

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        $payload = [
            // An empty map must stay a JSON object, never `[]`.
            'featureFlags' => $this->featureFlags === [] ? new stdClass() : $this->featureFlags,
        ];

        if ($this->branding instanceof Branding) {
            $payload['branding'] = $this->branding->jsonSerialize();
        }

        return $payload;
    }

    /** @return array<string, mixed> */
    public static function jsonSchema(): array
    {
        return ['type' => 'object', 'required' => ['featureFlags'],
            'properties' => [
                'featureFlags' => ['type' => 'object', 'additionalProperties' => ['type' => 'boolean']],
                'branding' => ['$ref' => '#/$defs/Branding']],
            'additionalProperties' => false];
    }
}

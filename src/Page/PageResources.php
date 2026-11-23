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
            'featureFlags' => $this->featureFlags,
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
                'featureFlags' => ['oneOf' => [['type' => 'object', 'additionalProperties' => ['type' => 'boolean']], ['type' => 'array', 'maxItems' => 0]]],
                'branding' => ['$ref' => '#/$defs/Branding']],
            'additionalProperties' => false];
    }
}

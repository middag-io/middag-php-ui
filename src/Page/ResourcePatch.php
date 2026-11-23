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
use stdClass;

/**
 * Partial update to the shared page resources.
 *
 * Lets the server push a change to the feature flags alongside a Fragment
 * or ActionResult, without resending the whole PageResources envelope. The
 * wire payload omits what is unset and is always a JSON object (an empty
 * patch is `{}`); the client merges it into its current resources.
 *
 * @api
 */
final readonly class ResourcePatch implements JsonSerializable
{
    /**
     * @param array<string, bool> $featureFlags
     */
    public function __construct(
        public array $featureFlags = [],
    ) {}

    /** @return array<string, array<string, bool>>|stdClass */
    public function jsonSerialize(): array|stdClass
    {
        if ($this->featureFlags === []) {
            return new stdClass();
        }

        return ['featureFlags' => $this->featureFlags];
    }

    /** @return array<string, mixed> */
    public static function jsonSchema(): array
    {
        return ['type' => 'object',
            'properties' => [
                'featureFlags' => ['type' => 'object', 'additionalProperties' => ['type' => 'boolean']],
            ],
            'additionalProperties' => false];
    }
}

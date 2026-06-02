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
use Middag\Ui\Block\Contract\BlockDescriptorInterface;
use Middag\Ui\Shared\Enum\RegionUpdateMode;

/**
 * A partial update to a single named region of a page the client owns.
 *
 * Used as a Fragment payload (kind REGION) to change a region's blocks without
 * reloading the page — e.g. after filtering or paginating. The mode decides
 * how the client applies it; block-bearing modes read `blocks`, REMOVE reads
 * `keys`. UPDATE matches each given block against the existing block sharing
 * its key.
 *
 * @api
 */
final readonly class RegionUpdate implements JsonSerializable
{
    /**
     * @param BlockDescriptorInterface[] $blocks Blocks for REPLACE/APPEND/PREPEND/UPDATE
     * @param string[]                   $keys   Target block keys for REMOVE
     */
    public function __construct(
        public string $region,
        public RegionUpdateMode $mode = RegionUpdateMode::REPLACE,
        public array $blocks = [],
        public array $keys = [],
    ) {}

    public static function replace(string $region, BlockDescriptorInterface ...$blocks): self
    {
        return new self($region, RegionUpdateMode::REPLACE, $blocks);
    }

    public static function append(string $region, BlockDescriptorInterface ...$blocks): self
    {
        return new self($region, RegionUpdateMode::APPEND, $blocks);
    }

    public static function prepend(string $region, BlockDescriptorInterface ...$blocks): self
    {
        return new self($region, RegionUpdateMode::PREPEND, $blocks);
    }

    public static function update(string $region, BlockDescriptorInterface ...$blocks): self
    {
        return new self($region, RegionUpdateMode::UPDATE, $blocks);
    }

    public static function remove(string $region, string ...$keys): self
    {
        return new self($region, RegionUpdateMode::REMOVE, keys: $keys);
    }

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        $payload = [
            'region' => $this->region,
            'mode' => $this->mode->value,
        ];

        if ($this->blocks !== []) {
            $payload['blocks'] = array_map(
                static fn (BlockDescriptorInterface $block): array => $block->jsonSerialize(),
                $this->blocks,
            );
        }

        if ($this->keys !== []) {
            $payload['keys'] = $this->keys;
        }

        return $payload;
    }

    /** @return array<string, mixed> */
    public static function jsonSchema(): array
    {
        return ['type' => 'object', 'required' => ['region', 'mode'],
            'properties' => ['region' => ['type' => 'string'], 'mode' => ['$ref' => '#/$defs/RegionUpdateMode'],
                'blocks' => ['type' => 'array', 'items' => ['$ref' => '#/$defs/BlockDescriptor']],
                'keys' => ['type' => 'array', 'items' => ['type' => 'string']]],
            'additionalProperties' => false];
    }
}

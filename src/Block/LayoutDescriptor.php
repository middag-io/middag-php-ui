<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Block;

use Middag\Ui\Block\Contract\BlockDescriptorInterface;
use Middag\Ui\Block\Contract\LayoutDescriptorInterface;

/**
 * Page layout: a template plus named regions, each holding ordered blocks.
 *
 * @api
 */
readonly class LayoutDescriptor implements LayoutDescriptorInterface
{
    /**
     * @param array<string, array<BlockDescriptorInterface>> $regions
     * @param array<string, mixed>                           $meta
     */
    public function __construct(
        public string $template,
        public array $regions,
        public array $meta = [],
    ) {}

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        $serializedRegions = array_filter(array_map(
            static fn (array $blocks): array => array_map(
                static fn (BlockDescriptorInterface $block): array => $block->jsonSerialize(),
                $blocks,
            ),
            $this->regions,
        ));

        $payload = [
            'template' => $this->template,
            'regions' => $serializedRegions,
        ];

        if ($this->meta !== []) {
            $payload['meta'] = $this->meta;
        }

        return $payload;
    }

    /** @return array<string, mixed> */
    public static function jsonSchema(): array
    {
        return ['type' => 'object', 'required' => ['template', 'regions'],
            'properties' => ['template' => ['type' => 'string'], 'regions' => ['oneOf' => [['type' => 'object', 'additionalProperties' => ['type' => 'array', 'items' => ['$ref' => '#/$defs/BlockDescriptor']]], ['type' => 'array', 'maxItems' => 0]]], 'meta' => ['type' => 'object', 'additionalProperties' => true]],
            'additionalProperties' => false];
    }
}

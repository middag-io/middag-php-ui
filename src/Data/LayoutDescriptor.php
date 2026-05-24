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

use Middag\Ui\Contract\BlockDescriptorInterface;
use Middag\Ui\Contract\LayoutDescriptorInterface;

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
}

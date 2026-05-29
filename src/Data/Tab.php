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
use Middag\Ui\Contract\BlockDescriptorInterface;

/**
 * A single tab within a tabs container block.
 *
 * Carries the tab identity, its label (i18n intent or literal), and the
 * blocks rendered when the tab is active.
 *
 * @api
 */
final readonly class Tab implements JsonSerializable
{
    /**
     * @param BlockDescriptorInterface[] $blocks
     */
    public function __construct(
        public string $id,
        public string|Translatable $label,
        public array $blocks = [],
    ) {}

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'label' => Label::serialize($this->label),
            'blocks' => array_map(
                static fn (BlockDescriptorInterface $block): array => $block->jsonSerialize(),
                $this->blocks,
            ),
        ];
    }

    /** @return array<string, mixed> */
    public static function jsonSchema(): array
    {
        return ['type' => 'object', 'required' => ['id', 'label', 'blocks'],
            'properties' => ['id' => ['type' => 'string'], 'label' => ['$ref' => '#/$defs/Label'], 'blocks' => ['type' => 'array', 'items' => ['$ref' => '#/$defs/BlockDescriptor']]],
            'additionalProperties' => false];
    }
}

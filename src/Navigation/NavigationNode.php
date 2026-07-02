<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Navigation;

use JsonSerializable;
use Middag\Ui\Shared\ValueObject\Label;
use Middag\Ui\Shared\ValueObject\Translatable;

/**
 * A node in the navigation tree.
 *
 * Can represent any level: group (N1), section (N2), or item (N3).
 * Serialized for the client navigation component.
 *
 * @api
 */
readonly class NavigationNode implements JsonSerializable
{
    /**
     * @param string              $key         Unique dot-notation key (e.g. 'audience.segments.index')
     * @param string|Translatable $label       Display label (i18n intent or raw literal)
     * @param null|string         $icon        Client icon-set name
     * @param null|string         $href        URL (leaf nodes only)
     * @param null|string         $badge       Badge value (lazy-resolved before serialization)
     * @param bool                $active      Whether this node is active (server-resolved)
     * @param bool                $drilldown   Whether clicking replaces sidebar with children + back
     * @param bool                $collapsible Whether the group renders with a toggle to expand/collapse
     * @param bool                $defaultOpen Whether a collapsible group starts expanded (default: false)
     * @param self[]              $children    Child nodes
     * @param int                 $weight      Sort order (lower = higher)
     * @param null|string         $capability  Opaque authorization token mapped by the adapter (not a host
     *                                         API call); node hidden if the current user lacks it
     */
    public function __construct(
        public string $key,
        public string|Translatable $label,
        public ?string $icon = null,
        public ?string $href = null,
        public ?string $badge = null,
        public bool $active = false,
        public bool $drilldown = false,
        public bool $collapsible = false,
        public bool $defaultOpen = false,
        public array $children = [],
        public int $weight = 50,
        public ?string $capability = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        $payload = [
            'key' => $this->key,
            'label' => Label::serialize($this->label),
        ];

        if ($this->icon !== null) {
            $payload['icon'] = $this->icon;
        }

        if ($this->href !== null) {
            $payload['href'] = $this->href;
        }

        if ($this->badge !== null) {
            $payload['badge'] = $this->badge;
        }

        if ($this->active) {
            $payload['active'] = true;
        }

        if ($this->drilldown) {
            $payload['drilldown'] = true;
        }

        if ($this->collapsible) {
            $payload['collapsible'] = true;
        }

        if ($this->defaultOpen) {
            $payload['defaultOpen'] = true;
        }

        if ($this->children !== []) {
            $payload['children'] = array_map(
                static fn (self $child): array => $child->jsonSerialize(),
                $this->children,
            );
        }

        return $payload;
    }

    /** @return array<string, mixed> */
    public static function jsonSchema(): array
    {
        return ['type' => 'object', 'required' => ['key', 'label'],
            'properties' => ['key' => ['type' => 'string'], 'label' => ['$ref' => '#/$defs/Label'], 'icon' => ['type' => 'string'], 'href' => ['type' => 'string'], 'badge' => ['type' => 'string'], 'active' => ['const' => true], 'drilldown' => ['const' => true], 'collapsible' => ['const' => true], 'defaultOpen' => ['const' => true], 'children' => ['type' => 'array', 'items' => ['$ref' => '#/$defs/NavigationNode']]],
            'additionalProperties' => false];
    }
}

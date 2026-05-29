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

/**
 * A node in the navigation tree (ADR-807).
 *
 * Can represent any level: group (N1), section (N2), or item (N3).
 * Serialized for the React SidebarNav component via Inertia shared props.
 *
 * @api
 */
readonly class NavigationNode implements JsonSerializable
{
    /**
     * @param string      $key          Unique dot-notation key (e.g. 'audience.segments.index')
     * @param string      $label        Display label
     * @param null|string $icon         Lucide icon name
     * @param null|string $href         URL (leaf nodes only)
     * @param null|string $badge        Badge value (lazy-resolved before serialization)
     * @param bool        $active       Whether this node is active (server-resolved)
     * @param bool        $drilldown    Whether clicking replaces sidebar with children + back
     * @param bool        $collapsible  Whether the group renders with a toggle to expand/collapse
     * @param bool        $default_open Whether a collapsible group starts expanded (default: false)
     * @param self[]      $children     Child nodes
     * @param int         $weight       Sort order (lower = higher)
     * @param null|string $capability   Required capability (node hidden if user lacks it)
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
        public bool $default_open = false,
        public array $children = [],
        public int $weight = 50,
        public ?string $capability = null,
    ) {}

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

        if ($this->default_open) {
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
}

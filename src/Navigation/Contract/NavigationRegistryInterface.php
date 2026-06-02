<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Navigation\Contract;

use Closure;

/**
 * Registry for the 3-level navigation tree.
 *
 * Extensions register groups (N1), sections (N2), and items (N3) during boot().
 * The registry builds the full tree, filters by capability, resolves the active
 * state from the current route, and serializes for the client navigation component.
 *
 * @api
 */
interface NavigationRegistryInterface
{
    /**
     * Register a top-level group (N1 — journey group).
     *
     * @param string      $key    Unique key (e.g. 'audience', 'automation')
     * @param string      $label  Display label
     * @param null|string $icon   Client icon-set name
     * @param int         $weight Sort order (lower = higher)
     */
    public function group(string $key, string $label, ?string $icon = null, int $weight = 50): static;

    /**
     * Register a section within a group (N2 — extension/CC).
     *
     * Key must be dot-separated: '{group}.{section}' (e.g. 'audience.segments').
     *
     * @param string      $key    Dot-notation key
     * @param string      $label  Display label
     * @param null|string $icon   Client icon-set name
     * @param int         $weight Sort order within parent group
     */
    public function section(string $key, string $label, ?string $icon = null, int $weight = 50): static;

    /**
     * Register a leaf item within a section (N3 — page/action).
     *
     * Key must be dot-separated: '{group}.{section}.{item}' (e.g. 'audience.segments.index').
     *
     * @param string      $key         Dot-notation key
     * @param string      $label       Display label
     * @param string      $route       Route name (resolved to a URL by the host)
     * @param array       $routeParams Route parameters
     * @param null|string $icon        Client icon-set name
     * @param int         $weight      Sort order within parent section
     */
    public function item(string $key, string $label, string $route, array $routeParams = [], ?string $icon = null, int $weight = 50): static;

    /**
     * Set a capability requirement on any node.
     *
     * `$capability` is an opaque authorization token mapped by the adapter (not a
     * host API call). The node (and its children) is hidden if the current user
     * lacks it. Filtering happens server-side before serialization.
     */
    public function capability(string $nodeKey, string $capability): static;

    /**
     * Mark a group as collapsible.
     *
     * Collapsible groups render with a toggle to expand/collapse their content.
     * The collapsed state is persisted client-side (localStorage).
     * Useful for advanced/secondary sections that should not dominate the sidebar.
     *
     * @param string $groupKey    Group to make collapsible
     * @param bool   $defaultOpen Whether the group starts expanded (default: false)
     */
    public function collapsible(string $groupKey, bool $defaultOpen = false): static;

    /**
     * Mark a node as drill-down.
     *
     * When the user clicks a drill-down node, the sidebar replaces its content
     * with the node's children and a back button. This is an exceptional behavior
     * — the default is the static 3-level tree.
     */
    public function drilldown(string $nodeKey): static;

    /**
     * Set a lazy badge on any node.
     *
     * The closure is resolved only during serialization (build), not at registration time.
     *
     * @param string  $nodeKey  Node to badge
     * @param Closure $resolver Returns string|int badge value
     */
    public function badge(string $nodeKey, Closure $resolver): static;

    /**
     * Build the full navigation payload for the current user and active route.
     *
     * @param string $activeRoute Current route name (used to resolve activeKey)
     *
     * @return array{
     *     tree: list<array<string, mixed>>,
     *     activeKey: string,
     *     footer: list<array<string, mixed>>
     * } `tree` and `footer` are serialized {@see NavigationNode} arrays (footer = the
     *   secondary/bottom node list); `activeKey` is the dot-notation key of the node
     *   resolved active for `$activeRoute`
     */
    public function build(string $activeRoute): array;
}

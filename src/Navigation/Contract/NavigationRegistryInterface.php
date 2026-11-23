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
use InvalidArgumentException;
use Middag\Ui\Navigation\NavigationNode;
use Middag\Ui\Navigation\NavigationTree;
use Middag\Ui\Shared\ValueObject\Translatable;

/**
 * Registry for the 3-level navigation tree.
 *
 * Modules register groups (N1), sections (N2), and items (N3) during boot().
 * The registry builds the full tree, filters by capability, resolves the active
 * state from the current route, and serializes for the client navigation component.
 *
 * An implementable contract: implementations must use these parameter names,
 * so named arguments work against any implementation (see API-STABILITY.md).
 *
 * @api
 */
interface NavigationRegistryInterface
{
    /**
     * Register a top-level group (N1 — journey group).
     *
     * @param string              $key          Unique key (e.g. 'audience', 'automation')
     * @param string|Translatable $label        Display label, stored as given (never translated by the registry)
     * @param null|string         $icon         Client icon-set name
     * @param int                 $weight       Sort order (lower = higher)
     * @param null|string         $entityType   Client-defined entity type hint, passed to NavigationNode unchanged
     * @param null|string         $badgeVariant Client-defined badge variant, passed to NavigationNode unchanged
     * @param null|string         $statusColor  Client-defined status color, passed to NavigationNode unchanged
     */
    public function group(
        string $key,
        string|Translatable $label,
        ?string $icon = null,
        int $weight = 50,
        ?string $entityType = null,
        ?string $badgeVariant = null,
        ?string $statusColor = null,
    ): static;

    /**
     * Register a section within a group (N2 — module/CC).
     *
     * Key must be dot-separated: '{group}.{section}' (e.g. 'audience.segments').
     *
     * @param string              $key          Dot-notation key
     * @param string|Translatable $label        Display label, stored as given (never translated by the registry)
     * @param null|string         $icon         Client icon-set name
     * @param int                 $weight       Sort order within parent group
     * @param null|string         $entityType   Client-defined entity type hint, passed to NavigationNode unchanged
     * @param null|string         $badgeVariant Client-defined badge variant, passed to NavigationNode unchanged
     * @param null|string         $statusColor  Client-defined status color, passed to NavigationNode unchanged
     */
    public function section(
        string $key,
        string|Translatable $label,
        ?string $icon = null,
        int $weight = 50,
        ?string $entityType = null,
        ?string $badgeVariant = null,
        ?string $statusColor = null,
    ): static;

    /**
     * Register a leaf item within a section (N3 — page/action).
     *
     * Key must be dot-separated: '{group}.{section}.{item}' (e.g. 'audience.segments.index').
     *
     * An item has exactly one destination: a `$route` (resolved to a URL by
     * the host) or an external `$href`. `$href` is used as given, never
     * through the host URL generator, and takes no `$routeParams`. It must be
     * an absolute URL that starts with the lowercase prefix `http://` or
     * `https://`, with a non-empty host and no whitespace or control
     * characters; relative, protocol-relative and other schemes are rejected.
     * An item without a route is never resolved as active.
     *
     * Example: `->item('help.docs', 'Docs', null, href: 'https://docs.example', target: '_blank')`.
     *
     * @param string               $key          Dot-notation key
     * @param string|Translatable  $label        Display label, stored as given (never translated by the registry)
     * @param null|string          $route        Route name (resolved to a URL by the host); null when `$href` is given
     * @param array<string, mixed> $routeParams  Route parameters; must be empty when `$href` is given
     * @param null|string          $icon         Client icon-set name
     * @param int                  $weight       Sort order within parent section
     * @param null|string          $entityType   Client-defined entity type hint, passed to NavigationNode unchanged
     * @param null|string          $badgeVariant Client-defined badge variant, passed to NavigationNode unchanged
     * @param null|string          $statusColor  Client-defined status color, passed to NavigationNode unchanged
     * @param null|string          $target       HTML link target (for example '_blank'), passed to NavigationNode unchanged
     * @param null|string          $href         External absolute http(s) URL; null when `$route` is given
     *
     * @throws InvalidArgumentException when both or neither of $route and $href are given, when $href is given
     *                                  with a non-empty $routeParams, or when $href is not an absolute lowercase
     *                                  http:// or https:// URL
     */
    public function item(
        string $key,
        string|Translatable $label,
        ?string $route,
        array $routeParams = [],
        ?string $icon = null,
        int $weight = 50,
        ?string $entityType = null,
        ?string $badgeVariant = null,
        ?string $statusColor = null,
        ?string $target = null,
        ?string $href = null,
    ): static;

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
     * @return NavigationTree `tree` and `footer` are {@see NavigationNode} lists (footer =
     *                        the secondary/bottom node list); `activeKey` is the dot-notation
     *                        key of the node resolved active for `$activeRoute`, or '' when none
     */
    public function build(string $activeRoute): NavigationTree;
}

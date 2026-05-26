<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui;

use LogicException;
use Middag\Ui\Contract\PageContractInterface;
use Middag\Ui\Contract\PageInterface;

/**
 * Base class for pages composed via the page contract pipeline (ADR-807).
 *
 * Extensions extend AbstractPage to declare a routable page. Subclasses set
 * the SLUG constant for container discovery and implement build() to compose
 * blocks/regions via a PageBuilder (or any PageContractInterface producer).
 *
 * Container resolution: any service implementing PageInterface is discoverable
 * by slug → FQN; the kernel binds `{slug}_page` aliases at boot time.
 *
 * @api
 */
abstract class AbstractPage implements PageInterface
{
    /**
     * Unique page slug used for service discovery and route binding.
     * Subclasses MUST override (empty string is rejected at runtime).
     */
    public const SLUG = '';

    /**
     * Returns the page slug, defaulting to the SLUG constant via late static binding.
     */
    public function slug(): string
    {
        return static::SLUG;
    }

    /**
     * Build the page contract.
     *
     * Single-page implementations compose the contract through a PageBuilder:
     *
     *   return PageBuilder::page(static::SLUG)
     *       ->title(...)
     *       ->layout(...)
     *       ->region('content', [...])
     *       ->build();
     *
     * Multi-method pages (one method per route, dispatched by the controller)
     * do not use this entry point and inherit this default, which throws if
     * invoked. Override only when the page has a single canonical contract.
     */
    public function build(): PageContractInterface
    {
        throw new LogicException(sprintf(
            '%s does not implement build(); it is a multi-method page dispatched by named controller methods.',
            static::class,
        ));
    }
}

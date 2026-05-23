<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.com.br>
 * @copyright   2026 MIDDAG (https://www.middag.com.br)
 * @license     Apache-2.0
 */

namespace Middag\Ui;

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
     * Implementations typically compose the contract through a PageBuilder:
     *
     *   return PageBuilder::page(static::SLUG)
     *       ->title(...)
     *       ->layout(...)
     *       ->region('content', [...])
     *       ->build();
     */
    abstract public function build(): PageContractInterface;
}

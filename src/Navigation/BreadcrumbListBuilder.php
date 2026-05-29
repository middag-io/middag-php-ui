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

/**
 * Fluent builder for breadcrumb arrays.
 *
 * @api
 */
class BreadcrumbListBuilder implements BreadcrumbListBuilderInterface
{
    /** @var BreadcrumbInterface[] */
    private array $items = [];

    /**
     * Add a breadcrumb with a link.
     */
    public function item(string $label, ?string $href = null): static
    {
        $this->items[] = new Breadcrumb($label, $href);

        return $this;
    }

    /**
     * Add the current (last) breadcrumb without a link.
     */
    public function current(string $label): static
    {
        $this->items[] = new Breadcrumb($label);

        return $this;
    }

    /**
     * @return BreadcrumbInterface[]
     */
    public function all(): array
    {
        return $this->items;
    }
}

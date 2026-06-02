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

/** @api */
interface BreadcrumbListBuilderInterface
{
    public function item(string $label, ?string $href = null): static;

    public function current(string $label): static;

    /** @return BreadcrumbInterface[] */
    public function all(): array;
}

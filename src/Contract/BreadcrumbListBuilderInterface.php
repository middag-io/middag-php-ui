<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.com.br>
 * @copyright   2026 MIDDAG (https://www.middag.com.br)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Contract;

/** @api */
interface BreadcrumbListBuilderInterface
{
    public function item(string $label, ?string $href = null): static;

    public function current(string $label): static;

    public function all(): array;
}

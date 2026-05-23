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

/**
 * Contract for extension dashboard widgets.
 *
 * @api
 */
interface DashboardWidgetInterface
{
    public function getSlug(): string;

    public function getTitle(): string;

    public function getExtension(): string;

    public function getPriority(): int;

    public function render(): string;
}

<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.com.br>
 * @copyright   2026 MIDDAG (https://www.middag.com.br)
 * @license     proprietary
 */

namespace Middag\Ui\Contract;

/**
 * Contract for extension dashboard widgets.
 *
 * @api
 */
interface DashboardWidgetInterface
{
    public function get_slug(): string;

    public function get_title(): string;

    public function get_extension(): string;

    public function get_priority(): int;

    public function render(): string;
}

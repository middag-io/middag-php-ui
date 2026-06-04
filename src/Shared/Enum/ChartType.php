<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Shared\Enum;

use Middag\Ui\Shared\ProvidesJsonSchema;

/**
 * Closed catalog of chart block render types (line/bar/area).
 *
 * Kept intentionally minimal so the wire contract stays small and stable;
 * hosts/adapters may extend the rendered chart vocabulary downstream.
 *
 * @api
 */
enum ChartType: string
{
    use ProvidesJsonSchema;

    case LINE = 'line';

    case BAR = 'bar';

    case AREA = 'area';
}

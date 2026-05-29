<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Enum;

use Middag\Ui\Enum\Concerns\ProvidesJsonSchema;

/**
 * Closed catalog of chart block render types.
 *
 * @api
 */
enum ChartType: string
{
    use ProvidesJsonSchema;

    case LINE = 'line';

    case BAR = 'bar';

    case AREA = 'area';

    case PIE = 'pie';

    case DONUT = 'donut';
}

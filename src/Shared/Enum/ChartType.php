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
 * Closed catalog of FREE chart block render types (basic line/bar/area).
 *
 * The advanced `pie` variant is PREMIUM and lives in the core premium
 * ChartType ({@see \Middag\Core\Ui\Shared\Enum\ChartType}). Keep this FREE
 * enum limited to line/bar/area so the Community wire contract stays clean.
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

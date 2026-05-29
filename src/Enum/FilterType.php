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

/**
 * Closed catalog of table filter control types.
 *
 * Each value maps to a client filter component; the library only declares the
 * intent.
 *
 * @api
 */
enum FilterType: string
{
    case SELECT = 'select';

    case TEXT = 'text';

    case DATE = 'date';

    case DATE_RANGE = 'date_range';

    case BOOLEAN = 'boolean';

    case NUMBER_RANGE = 'number_range';
}

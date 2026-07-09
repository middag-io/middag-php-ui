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

use Middag\Ui\Shared\Concerns\ProvidesJsonSchema;

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
    use ProvidesJsonSchema;

    case Select = 'select';

    case Text = 'text';

    case Date = 'date';

    case DateRange = 'date_range';

    case Boolean = 'boolean';

    case NumberRange = 'number_range';
}

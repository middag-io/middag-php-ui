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
 * Formatting intent for a displayed value.
 *
 * The library only declares the type plus options; the client formats via
 * `Intl` and the user's locale/timezone/number preferences. It never formats
 * server-side.
 *
 * @api
 */
enum ValueFormat: string
{
    use ProvidesJsonSchema;

    case Text = 'text';

    case Date = 'date';

    case Datetime = 'datetime';

    case Time = 'time';

    case Number = 'number';

    case Currency = 'currency';

    case Boolean = 'boolean';

    case Badge = 'badge';
}

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
 * Display format of a `timestamp` column of a `dense_table` block
 * (`timestampFormat`); the client defaults to `relative`.
 *
 * An open enum: the client renderer falls back to a safe default for a value
 * it does not know, so a new value ships in a MINOR.
 *
 * @api
 */
enum TimestampFormat: string
{
    use ProvidesJsonSchema;

    case Relative = 'relative';

    case DateTime = 'datetime';

    case Date = 'date';

    case Time = 'time';

    case Iso = 'iso';
}

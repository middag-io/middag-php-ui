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
 * Semantic intent a `status` or `badge` cell resolves to through a column
 * `statusMap`.
 *
 * An open enum: the client renderer falls back to a safe default for a value
 * it does not know, so a new value ships in a MINOR.
 *
 * @api
 */
enum StatusIntent: string
{
    use ProvidesJsonSchema;

    case Success = 'success';

    case Warning = 'warning';

    case Destructive = 'destructive';

    case Secondary = 'secondary';

    case Info = 'info';

    case Default = 'default';
}

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
 * Kind of an `empty_state` block (`data.variant`): first use, no results, an
 * error or a missing permission.
 *
 * An open enum: the client renderer falls back to a safe default for a value
 * it does not know, so a new value ships in a MINOR.
 *
 * @api
 */
enum EmptyStateVariant: string
{
    use ProvidesJsonSchema;

    case FirstUse = 'first-use';

    case NoResults = 'no-results';

    case Error = 'error';

    case Permission = 'permission';
}

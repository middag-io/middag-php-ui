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
 * Presentation variant of a `card_grid` block (`data.variant`).
 *
 * An open enum: the client renderer falls back to the default card for a value
 * it does not know, so a new value ships in a MINOR.
 *
 * @api
 */
enum CardGridVariant: string
{
    use ProvidesJsonSchema;

    case Default = 'default';

    case Store = 'store';

    case Connector = 'connector';
}

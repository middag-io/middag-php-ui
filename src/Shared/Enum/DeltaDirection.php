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
 * How the client colours the `delta` of a `metric_card` block (`data.deltaDirection`).
 *
 * An open enum: the client renderer falls back to a safe default for a value
 * it does not know, so a new value ships in a MINOR.
 *
 * @api
 */
enum DeltaDirection: string
{
    use ProvidesJsonSchema;

    case Positive = 'positive';

    case Negative = 'negative';

    case Neutral = 'neutral';
}

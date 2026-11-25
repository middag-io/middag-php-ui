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
 * Overall tone of a `status_strip` block (`data.tone`).
 *
 * An open enum: the client renderer falls back to a safe default for a value
 * it does not know, so a new value ships in a MINOR.
 *
 * @api
 */
enum StatusStripTone: string
{
    use ProvidesJsonSchema;

    case Success = 'success';

    case Warning = 'warning';

    case Danger = 'danger';

    case Neutral = 'neutral';
}

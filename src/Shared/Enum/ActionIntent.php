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
 * Visual/semantic intent of an action control.
 *
 * Closed catalog; the client maps each value to a button style. Distinct from
 * {@see NotificationLevel} (success/info/warning/error), which classifies
 * feedback severity, not control emphasis.
 *
 * @api
 */
enum ActionIntent: string
{
    use ProvidesJsonSchema;

    case Primary = 'primary';

    case Secondary = 'secondary';

    case Danger = 'danger';

    case Success = 'success';

    case Warning = 'warning';

    case Info = 'info';

    case Link = 'link';

    case Ghost = 'ghost';
}

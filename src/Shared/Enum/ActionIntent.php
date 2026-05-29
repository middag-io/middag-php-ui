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

use Middag\Ui\Shared\Enum\Concerns\ProvidesJsonSchema;

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

    case PRIMARY = 'primary';

    case SECONDARY = 'secondary';

    case DANGER = 'danger';

    case SUCCESS = 'success';

    case WARNING = 'warning';

    case INFO = 'info';

    case LINK = 'link';

    case GHOST = 'ghost';
}

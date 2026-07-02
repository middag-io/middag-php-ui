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
 * Severity level of a user-facing notification (flash / toast).
 *
 * @api
 */
enum NotificationLevel: string
{
    use ProvidesJsonSchema;

    case SUCCESS = 'success';

    case INFO = 'info';

    case WARNING = 'warning';

    case ERROR = 'error';
}

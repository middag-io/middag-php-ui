<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Enum;

use Middag\Ui\Enum\Concerns\ProvidesJsonSchema;

/**
 * What a partial Fragment carries, so the client routes it without guessing.
 *
 * Closed catalogue. CUSTOM is the escape hatch: pair it with the Fragment's
 * customType to ship a host-specific payload kind the client knows how to
 * mount, without widening this enum.
 *
 * @api
 */
enum FragmentKind: string
{
    use ProvidesJsonSchema;

    case BLOCK = 'block';

    case REGION = 'region';

    case TABLE = 'table';

    case FORM = 'form';

    case DETAIL = 'detail';

    case INSPECTOR = 'inspector';

    case NOTIFICATIONS = 'notifications';

    case CUSTOM = 'custom';
}

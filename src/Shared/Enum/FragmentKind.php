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

    case Block = 'block';

    case Region = 'region';

    case Table = 'table';

    case Form = 'form';

    case Detail = 'detail';

    case Inspector = 'inspector';

    case Notifications = 'notifications';

    case Custom = 'custom';
}

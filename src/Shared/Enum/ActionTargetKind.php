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
 * Discriminator for an ActionTarget value object.
 *
 * - `link`    — navigate to a URL (href).
 * - `route`   — navigate to a host-named route resolved client-side.
 * - `request` — trigger an HTTP request (mutation).
 *
 * @api
 */
enum ActionTargetKind: string
{
    use ProvidesJsonSchema;

    case Link = 'link';

    case Route = 'route';

    case Request = 'request';
}

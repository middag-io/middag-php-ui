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

use Middag\Ui\Shared\ProvidesJsonSchema;

/**
 * User theme preference.
 *
 * Only the preference *value* is contract; applying it visually (CSS
 * dark-mode) is a pure-client concern, out of scope for the library.
 *
 * @api
 */
enum ThemeMode: string
{
    use ProvidesJsonSchema;

    case LIGHT = 'light';

    case DARK = 'dark';

    case SYSTEM = 'system';
}

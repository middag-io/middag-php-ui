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
    case LIGHT = 'light';

    case DARK = 'dark';

    case SYSTEM = 'system';
}

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
 * HTTP method for a request-target action.
 *
 * @api
 */
enum HttpMethod: string
{
    case GET = 'get';

    case POST = 'post';

    case PUT = 'put';

    case PATCH = 'patch';

    case DELETE = 'delete';
}

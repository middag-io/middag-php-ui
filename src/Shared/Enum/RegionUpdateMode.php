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
 * How the client applies a RegionUpdate to an existing region.
 *
 * - REPLACE — swap the whole region content for the given blocks (empty clears it).
 * - APPEND  — add the given blocks after the current ones (infinite scroll).
 * - PREPEND — add the given blocks before the current ones (insert at top).
 * - REMOVE  — drop the blocks whose key matches one of the given keys.
 * - UPDATE  — replace in place the blocks whose key matches a given block's key.
 *
 * REMOVE reads the keys list; the other modes read the blocks list.
 *
 * @api
 */
enum RegionUpdateMode: string
{
    use ProvidesJsonSchema;

    case REPLACE = 'replace';

    case APPEND = 'append';

    case PREPEND = 'prepend';

    case REMOVE = 'remove';

    case UPDATE = 'update';
}

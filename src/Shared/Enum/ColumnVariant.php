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
 * Cell renderer selected for a `dense_table` column.
 *
 * Distinct from {@see ValueFormat}, and the two are not interchangeable:
 * `format` says how to render a scalar (a date as `dd/mm`, a number as
 * currency) and the client resolves it through `Intl`; `variant` says which
 * CELL COMPONENT receives the value, and several of them read a structured
 * value rather than a scalar — `rich_status` takes `{label, appearance}`,
 * `annotated` and `progress` take their own shapes.
 *
 * The renderer is what the React `DenseTableBlock` reads off each column
 * (`col.variant`); it never looks at `format`. A column that needs a coloured
 * pill therefore has to declare the variant — `ValueFormat::Badge` alone
 * leaves the cell as plain text.
 *
 * @api
 */
enum ColumnVariant: string
{
    use ProvidesJsonSchema;

    case Text = 'text';

    /** Pill whose colour is resolved from the displayed text against a fixed English vocabulary. */
    case Status = 'status';

    case Badge = 'badge';

    case Boolean = 'boolean';

    case Timestamp = 'timestamp';

    case Link = 'link';

    /** Pill from a `{label, appearance}` value — colour decoupled from the (translatable) text. */
    case RichStatus = 'rich_status';

    case Html = 'html';

    case LinkGroup = 'link_group';

    case Annotated = 'annotated';

    case Progress = 'progress';
}

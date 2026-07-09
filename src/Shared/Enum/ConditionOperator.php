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
 * Closed catalog of condition operators for form field visibility rules.
 *
 * Used to declare reactive conditions between form fields. Host renderer
 * adapters decide which operators they can natively express.
 *
 * @api
 */
enum ConditionOperator: string
{
    use ProvidesJsonSchema;

    // ── Equality ──────────────────────────────
    case Eq = 'eq';

    case Neq = 'neq';

    // ── Set membership ────────────────────────
    case In = 'in';

    case NotIn = 'not_in';

    // ── Numeric comparison ────────────────────
    case Gt = 'gt';

    case Gte = 'gte';

    case Lt = 'lt';

    case Lte = 'lte';

    // ── Boolean / presence ────────────────────
    case Truthy = 'truthy';

    case Falsy = 'falsy';

    case Exists = 'exists';

    case Empty = 'empty';

    // ── Pattern ───────────────────────────────
    case Matches = 'matches';
}

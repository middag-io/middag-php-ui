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
    case EQ = 'eq';

    case NEQ = 'neq';

    // ── Set membership ────────────────────────
    case IN = 'in';

    case NOT_IN = 'not_in';

    // ── Numeric comparison ────────────────────
    case GT = 'gt';

    case GTE = 'gte';

    case LT = 'lt';

    case LTE = 'lte';

    // ── Boolean / presence ────────────────────
    case TRUTHY = 'truthy';

    case FALSY = 'falsy';

    case EXISTS = 'exists';

    case EMPTY = 'empty';

    // ── Pattern ───────────────────────────────
    case MATCHES = 'matches';
}

<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.com.br>
 * @copyright   2026 MIDDAG (https://www.middag.com.br)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Enum;

/**
 * Closed catalog of condition operators for form field visibility rules (ADR-806).
 *
 * Used to declare reactive conditions between form fields.
 * MATCHES is intentionally excluded from mform compatibility because
 * mform cannot natively express regex-based conditions.
 *
 * @api
 */
enum ConditionOperator: string
{
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

    /**
     * Whether mform can natively express this operator in a condition.
     *
     * Returns false for MATCHES because mform has no built-in regex support.
     * All other operators map to mform disabledIf / hideIf semantics.
     */
    public function is_mform_compatible(): bool
    {
        return $this !== self::MATCHES;
    }
}

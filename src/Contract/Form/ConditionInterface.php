<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Contract\Form;

use Middag\Ui\Data\Form\Condition as condition;

/**
 * Contract for objects that can produce a condition value object.
 *
 * Implemented by fluent DSL builders in the field layer that expose
 * conditional visibility/requirement/disability rules (ADR-806).
 *
 * @internal
 */
interface ConditionInterface
{
    public function toCondition(): condition;
}

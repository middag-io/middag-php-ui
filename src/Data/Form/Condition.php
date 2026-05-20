<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.com.br>
 * @copyright   2026 MIDDAG (https://www.middag.com.br)
 * @license     proprietary
 */

namespace Middag\Ui\Data\Form;

use Middag\Ui\Enum\ConditionOperator as condition_operator;

/**
 * Conditional rule attached to a field (visible_when, required_when, etc.).
 *
 * @internal
 */
final readonly class Condition
{
    public const KIND_VISIBLE_WHEN = 'visible_when';

    public const KIND_HIDDEN_WHEN = 'hidden_when';

    public const KIND_REQUIRED_WHEN = 'required_when';

    public const KIND_DISABLED_WHEN = 'disabled_when';

    public function __construct(
        public string $field,
        public condition_operator $operator,
        public mixed $value,
        public string $kind = self::KIND_VISIBLE_WHEN,
    ) {}
}

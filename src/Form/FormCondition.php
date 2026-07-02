<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Form;

/**
 * A reactive condition on a form field: visibility/state driven by another
 * field's value (`visible_when`, `hidden_when`, `disabled_when`, `required_when`).
 *
 * A schema-only value shape (no instances) — it is referenced from
 * {@see FieldPropsBase}. The wire operator vocabulary is the narrow set
 * the @middag-io/react FormPanel evaluates client-side (`equals`,
 * `not_equals`, `in`, `not_in`), which is deliberately distinct from the
 * richer server-side {@see ConditionOperator} catalog.
 *
 * @api
 */
final class FormCondition
{
    private function __construct() {}

    /** @return array<string, mixed> */
    public static function jsonSchema(): array
    {
        return [
            'type' => 'object',
            'required' => ['field', 'operator', 'value'],
            'properties' => [
                'field' => ['type' => 'string'],
                'operator' => ['enum' => ['equals', 'not_equals', 'in', 'not_in']],
                'value' => [
                    'oneOf' => [
                        ['type' => 'string'],
                        ['type' => 'array', 'items' => ['type' => 'string']],
                    ],
                ],
            ],
            'additionalProperties' => false,
        ];
    }
}

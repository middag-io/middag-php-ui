<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Action;

use Middag\Ui\Form\FormCondition;

/**
 * A row-level action: the canonical {@see Action} plus per-row conditions,
 * evaluated by the client against each row of a table or card grid.
 *
 * `visible_when`, `disabled_when` and `loading_when` take a
 * {@see FormCondition}; `disabled_reason_field` names the row
 * field that holds the tooltip shown while the action is disabled; `variant`
 * draws it as an icon or a button.
 *
 * A schema-only value shape (no instances). A plain serialized {@see Action}
 * is a valid ConditionalAction.
 *
 * @api
 */
final class ConditionalAction
{
    private function __construct() {}

    /** @return array<string, mixed> */
    public static function jsonSchema(): array
    {
        $schema = Action::jsonSchema();
        $schema['properties'] = [
            ...$schema['properties'],
            'variant' => ['$ref' => '#/$defs/ConditionalActionVariant'],
            'visible_when' => ['$ref' => '#/$defs/FormCondition'],
            'disabled_when' => ['$ref' => '#/$defs/FormCondition'],
            'loading_when' => ['$ref' => '#/$defs/FormCondition'],
            'disabled_reason_field' => ['type' => 'string'],
        ];

        return $schema;
    }
}

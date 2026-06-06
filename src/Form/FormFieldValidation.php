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
 * Client-side validation rules for a form field, additive to the type-based
 * defaults the renderer derives from the component.
 *
 * A schema-only value shape (no instances) — referenced from
 * {@see FieldPropsBase} via the `validation` prop. Mirrors @middag-io/react's
 * `FormFieldValidation`.
 *
 * @api
 */
final class FormFieldValidation
{
    private function __construct() {}

    /** @return array<string, mixed> */
    public static function jsonSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'minLength' => ['type' => 'integer'],
                'maxLength' => ['type' => 'integer'],
                'pattern' => ['type' => 'string'],
                'patternMessage' => ['type' => 'string'],
            ],
            'additionalProperties' => false,
        ];
    }
}

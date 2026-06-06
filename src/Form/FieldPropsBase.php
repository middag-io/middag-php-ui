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
 * Base props shared by every interactive form field component.
 *
 * A schema-only value shape (no instances): the per-component `props` of a
 * {@see FormFieldNode} branch is either this base ($ref) or this base
 * intersected (`allOf`) with component-specific props. Mirrors
 * the @middag-io/react `FieldPropsBase`.
 *
 * NOTE: this schema intentionally omits `additionalProperties` (it stays open)
 * so it can be composed under `allOf` with component-specific props without the
 * JSON Schema allOf + additionalProperties:false pitfall, and so the wire stays
 * lenient (D-04:A). The generated TypeScript is still closed — the react codegen
 * compiles objects with no `additionalProperties` as closed types.
 *
 * @api
 */
final class FieldPropsBase
{
    private function __construct() {}

    /** @return array<string, mixed> */
    public static function jsonSchema(): array
    {
        return [
            'type' => 'object',
            'required' => ['label'],
            'properties' => [
                'label' => ['type' => 'string'],
                'placeholder' => ['type' => 'string'],
                'helpText' => ['type' => 'string'],
                'required' => ['type' => 'boolean'],
                'disabled' => ['type' => 'boolean'],
                'readOnly' => ['type' => 'boolean'],
                'validation' => ['$ref' => '#/$defs/FormFieldValidation'],
                'visible_when' => ['$ref' => '#/$defs/FormCondition'],
                'hidden_when' => ['$ref' => '#/$defs/FormCondition'],
                'disabled_when' => ['$ref' => '#/$defs/FormCondition'],
                'required_when' => ['$ref' => '#/$defs/FormCondition'],
            ],
        ];
    }
}

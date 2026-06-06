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
 * The form schema tree node union: a field, section, group or header.
 *
 * A schema-only umbrella (no instances) — the recursion anchor referenced by
 * {@see FormSectionNode} and {@see FormGroupNode} children, and the single
 * `$def` @middag-io/react codegen turns into the `FormSchemaNode` discriminated
 * union. Standalone by design: it is NOT referenced from the PageContract /
 * Fragment trees (the form-panel block payload stays a lenient passthrough
 * during the migration window, D-04:A), so the recursive cycle never reaches
 * the zod roots — only the TypeScript types and the opis roundtrip.
 *
 * @api
 */
final class FormSchemaNode
{
    private function __construct() {}

    /** @return array<string, mixed> */
    public static function jsonSchema(): array
    {
        return [
            'oneOf' => [
                ['$ref' => '#/$defs/FormFieldNode'],
                ['$ref' => '#/$defs/FormSectionNode'],
                ['$ref' => '#/$defs/FormGroupNode'],
                ['$ref' => '#/$defs/FormHeaderNode'],
            ],
        ];
    }
}

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
 * A host-defined input mask for a national-document field, merged with the
 * built-in masks the @middag-io/react DocumentField ships.
 *
 * A schema-only value shape (no instances) — referenced from the `document`
 * component's `documentMasks` map in {@see FormFieldNode}. Mirrors
 * the @middag-io/react `FormFieldDocumentMask`.
 *
 * @api
 */
final class FormFieldDocumentMask
{
    private function __construct() {}

    /** @return array<string, mixed> */
    public static function jsonSchema(): array
    {
        return [
            'type' => 'object',
            'required' => ['pattern', 'maxLength'],
            'properties' => [
                'pattern' => ['type' => 'string'],
                'maxLength' => ['type' => 'integer'],
                'placeholder' => ['type' => 'string'],
            ],
            'additionalProperties' => false,
        ];
    }
}

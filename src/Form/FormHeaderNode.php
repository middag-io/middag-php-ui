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

use Middag\Ui\Form\Contract\FormSchemaNodeInterface;

/**
 * A non-interactive section header separating fields in a form schema tree.
 *
 * Wire shape: `{ kind: 'header', label }`. Mirrors @middag-io/react's
 * `FormHeaderNode` (the tree-level header; distinct from the `header`
 * {@see FormFieldNode} component).
 *
 * @api
 */
final readonly class FormHeaderNode implements FormSchemaNodeInterface
{
    public function __construct(
        public string $label,
    ) {}

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        return ['kind' => 'header', 'label' => $this->label];
    }

    /** @return array<string, mixed> */
    public static function jsonSchema(): array
    {
        return [
            'type' => 'object',
            'required' => ['kind', 'label'],
            'properties' => [
                'kind' => ['const' => 'header'],
                'label' => ['type' => 'string'],
            ],
            'additionalProperties' => false,
        ];
    }
}

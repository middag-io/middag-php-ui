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
 * A horizontal group laying its child nodes out in 1 or 2 columns.
 *
 * Wire shape: `{ kind: 'group', id, columns?, children }`. Mirrors
 * the @middag-io/react `FormGroupNode`.
 *
 * @api
 */
final readonly class FormGroupNode implements FormSchemaNodeInterface
{
    /**
     * @param array<int, FormSchemaNodeInterface> $children
     * @param null|int                            $columns  1 or 2; omitted when null
     */
    public function __construct(
        public string $id,
        public array $children = [],
        public ?int $columns = null,
    ) {}

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        $payload = [
            'kind' => 'group',
            'id' => $this->id,
        ];

        if ($this->columns !== null) {
            $payload['columns'] = $this->columns;
        }

        $payload['children'] = array_map(
            static fn (FormSchemaNodeInterface $child): array => $child->jsonSerialize(),
            $this->children,
        );

        return $payload;
    }

    /** @return array<string, mixed> */
    public static function jsonSchema(): array
    {
        return [
            'type' => 'object',
            'required' => ['kind', 'id', 'children'],
            'properties' => [
                'kind' => ['const' => 'group'],
                'id' => ['type' => 'string'],
                'columns' => ['enum' => [1, 2]],
                'children' => ['type' => 'array', 'items' => ['$ref' => '#/$defs/FormSchemaNode']],
            ],
            'additionalProperties' => false,
        ];
    }
}

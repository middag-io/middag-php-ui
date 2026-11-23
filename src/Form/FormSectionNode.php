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
use Middag\Ui\Shared\ValueObject\Label;
use Middag\Ui\Shared\ValueObject\Translatable;

/**
 * A collapsible section grouping nodes under a label.
 *
 * Wire shape: `{ kind: 'section', id, label, defaultCollapsed?, children }`.
 * Mirrors @middag-io/react's `FormSectionNode`. `label` is a literal string, a
 * {@see Translatable} (serialized as `{key, domain, params?}`) or null (an
 * unlabelled section, serialized as `label: null`).
 *
 * @api
 */
final readonly class FormSectionNode implements FormSchemaNodeInterface
{
    /**
     * @param array<int, FormSchemaNodeInterface> $children
     */
    public function __construct(
        public string $id,
        public string|Translatable|null $label,
        public array $children = [],
        public bool $defaultCollapsed = false,
    ) {}

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        $payload = [
            'kind' => 'section',
            'id' => $this->id,
            'label' => Label::serializeNullable($this->label),
            'children' => array_map(
                static fn (FormSchemaNodeInterface $child): array => $child->jsonSerialize(),
                $this->children,
            ),
        ];

        if ($this->defaultCollapsed) {
            $payload['defaultCollapsed'] = true;
        }

        return $payload;
    }

    /** @return array<string, mixed> */
    public static function jsonSchema(): array
    {
        return [
            'type' => 'object',
            'required' => ['kind', 'id', 'label', 'children'],
            'properties' => [
                'kind' => ['const' => 'section'],
                'id' => ['type' => 'string'],
                'label' => ['oneOf' => [['type' => 'string'], ['$ref' => '#/$defs/Translatable'], ['type' => 'null']]],
                // `boolean` (not const:true) for parity with the react FormSectionNode
                // type; the serializer still omits it when false (wire carries
                // true-or-absent), but a consumer's `defaultCollapsed: false` stays
                // a valid value of the type.
                'defaultCollapsed' => ['type' => 'boolean'],
                'children' => ['type' => 'array', 'items' => ['$ref' => '#/$defs/FormSchemaNode']],
            ],
            'additionalProperties' => false,
        ];
    }
}

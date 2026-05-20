<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.com.br>
 * @copyright   2026 MIDDAG (https://www.middag.com.br)
 * @license     proprietary
 */

namespace Middag\Ui\Infrastructure\Form\Renderer;

use Middag\Ui\Data\Form\Condition as condition;
use Middag\Ui\Data\Form\FieldDefinition as field_definition;
use Middag\Ui\Enum\FieldType as field_type;

/**
 * Maps a field_definition to a Vue component name + props array for InertiaRenderer.
 *
 * Label and help keys are passed through as-is; the Vue layer resolves i18n
 * client-side or receives pre-resolved strings from the server.
 *
 * @internal
 */
final class InertiaFieldMapper
{
    /**
     * Map a field_definition to a [component => string, props => array] pair.
     *
     * @return array{component: string, props: array<string, mixed>}
     */
    public function map(field_definition $def): array
    {
        $component = $this->resolve_component($def->type);
        $props = $this->build_props($def);

        return ['component' => $component, 'props' => $props];
    }

    /** Resolve the Vue component name for a given field_type. */
    private function resolve_component(field_type $type): string
    {
        return match ($type) {
            field_type::TEXT,
            field_type::EMAIL,
            field_type::URL => 'TextField',
            field_type::TEXTAREA => 'TextAreaField',
            field_type::PASSWORD => 'PasswordField',
            field_type::INT,
            field_type::FLOAT => 'NumberField',
            field_type::SELECT => 'SelectField',
            field_type::MULTISELECT => 'MultiSelectField',
            field_type::RADIO => 'RadioField',
            field_type::CHECKBOX => 'CheckboxField',
            field_type::SWITCH => 'SwitchField',
            field_type::DATE => 'DateField',
            field_type::DATETIME => 'DateTimeField',
            field_type::DURATION => 'DurationField',
            field_type::FILE => 'FileField',
            field_type::ENTITY_PICKER => 'EntityPickerField',
            field_type::HIDDEN => 'HiddenField',
            field_type::STATIC => 'StaticText',
            field_type::HEADER => 'SectionHeader',
        };
    }

    /**
     * Build the props array for the Vue component.
     *
     * @return array<string, mixed>
     */
    private function build_props(field_definition $def): array
    {
        return [
            'name' => $def->name,
            'label' => $def->label,
            'help' => $def->help,
            'required' => $def->required,
            'default' => $def->default,
            'attributes' => $def->attributes,
            'options' => $def->options,
            'conditions' => array_map(
                static fn (condition $c): array => [
                    'field' => $c->field,
                    'op' => $c->operator->value,
                    'value' => $c->value,
                    'kind' => $c->kind,
                ],
                $def->conditions,
            ),
        ];
    }
}

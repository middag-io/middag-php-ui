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

use Middag\Ui\Contract\Form\FieldInterface as field_interface;
use Middag\Ui\Contract\Form\FormInterface as form_interface;
use Middag\Ui\Contract\Form\FormRendererInterface as form_renderer_interface;
use Middag\Ui\Contract\Form\LayoutElementInterface as layout_element_interface;
use Middag\Ui\Data\Form\RendererOutput as renderer_output;
use Middag\Ui\Enum\RenderTarget as render_target;
use Middag\Ui\Infrastructure\Form\Layout\Group as group;
use Middag\Ui\Infrastructure\Form\Layout\Section as section;

/**
 * Inertia (Vue SPA) form renderer adapter (ADR-805).
 *
 * Serializes the form schema into a props array consumed by the Inertia
 * front-end. Each schema item becomes a typed node: field, section, or group.
 *
 * Produces renderer_output::props() with shape:
 *   { schema: [...], values: {...}, errors: {...}, meta: {} }
 *
 * @internal
 */
final readonly class InertiaRenderer implements form_renderer_interface
{
    public function __construct(private InertiaFieldMapper $mapper) {}

    /** {@inheritdoc} */
    public static function target(): render_target
    {
        return render_target::INERTIA;
    }

    /** {@inheritdoc} */
    public function render(form_interface $form): renderer_output
    {
        $state = $form->state();
        $schema = $this->serialize_schema($form->schema());

        $props = [
            'schema' => $schema,
            'values' => $state->values(),
            'errors' => $state->errors(),
            'meta' => [],
        ];

        return renderer_output::props(render_target::INERTIA, $props);
    }

    /**
     * Recursively serialize a schema array into serializable nodes.
     *
     * @param array<int, field_interface|layout_element_interface> $items
     *
     * @return array<int, array<string, mixed>>
     */
    private function serialize_schema(array $items): array
    {
        $nodes = [];
        foreach ($items as $item) {
            if ($item instanceof section) {
                $nodes[] = [
                    'kind' => 'section',
                    'id' => $item->id(),
                    'label' => $item->label_data(),
                    'children' => $this->serialize_schema($item->children()),
                ];
            } elseif ($item instanceof group) {
                $nodes[] = [
                    'kind' => 'group',
                    'id' => $item->id(),
                    'children' => $this->serialize_schema($item->children()),
                ];
            } elseif ($item instanceof layout_element_interface) {
                // Unknown layout element — serialize as generic group-like node.
                $nodes[] = [
                    'kind' => 'group',
                    'id' => $item->id(),
                    'children' => $this->serialize_schema($item->children()),
                ];
            } elseif ($item instanceof field_interface) {
                $mapped = $this->mapper->map($item->to_definition());
                $nodes[] = [
                    'kind' => 'field',
                    'component' => $mapped['component'],
                    'props' => $mapped['props'],
                ];
            }
        }

        return $nodes;
    }
}

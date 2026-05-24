<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Data\Form;

use Middag\Ui\Enum\FieldType;

/**
 * Neutral, immutable representation of a form field.
 *
 * Renderers (mform, inertia, future adapters) consume this value object
 * — never the fluent DSL builder. This is the boundary between the
 * public DSL (base/form/field) and the infrastructure renderers.
 *
 * @internal
 */
final readonly class FieldDefinition
{
    /**
     * @param string                                     $name
     * @param FieldType                                  $type
     * @param null|array{key: string, component: string} $label
     * @param null|array{key: string, component: string} $help
     * @param mixed                                      $default
     * @param bool                                       $required
     * @param array<string, mixed>                       $attributes
     * @param array<int, Condition>                      $conditions
     * @param array<int|string, mixed>                   $options
     */
    public function __construct(
        public string $name,
        public FieldType $type,
        public ?array $label,
        public ?array $help,
        public mixed $default,
        public bool $required,
        public array $attributes,
        public array $conditions,
        public array $options,
    ) {}
}

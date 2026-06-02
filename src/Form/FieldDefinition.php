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

use Middag\Ui\Condition\Condition;
use Middag\Ui\Shared\Data\Translatable;
use Middag\Ui\Shared\Enum\FieldType;

/**
 * Neutral, immutable representation of a form field.
 *
 * Host renderer adapters consume this value object — never the fluent field
 * builder. This is the boundary between the public field builder (the host's
 * field DSL, which produces this via {@see FieldInterface::toDefinition()}) and
 * the infrastructure renderers.
 *
 * @api
 */
final readonly class FieldDefinition
{
    /**
     * @param string                   $name
     * @param FieldType                $type
     * @param null|string|Translatable $label       UI label (i18n intent or raw literal)
     * @param null|string|Translatable $help        UI help text (i18n intent or raw literal)
     * @param mixed                    $default
     * @param FieldConstraints         $constraints Typed client-side validation constraints
     * @param array<string, mixed>     $attributes
     * @param array<int, Condition>    $conditions
     * @param array<int|string, mixed> $options
     */
    public function __construct(
        public string $name,
        public FieldType $type,
        public string|Translatable|null $label,
        public string|Translatable|null $help,
        public mixed $default,
        public FieldConstraints $constraints,
        public array $attributes,
        public array $conditions,
        public array $options,
    ) {}
}

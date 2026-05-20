<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.com.br>
 * @copyright   2026 MIDDAG (https://www.middag.com.br)
 * @license     proprietary
 */

namespace Middag\Ui\Contract\Form;

use Middag\Ui\Data\Form\FieldDefinition as field_definition;

/**
 * Contract for fields. Implemented by abstract_field and all concrete field classes.
 *
 * The fluent DSL builder must be able to produce an immutable field_definition
 * consumed by renderer adapters (ADR-806).
 *
 * @api
 */
interface FieldInterface
{
    /** Final immutable representation consumed by renderers. */
    public function to_definition(): field_definition;

    public function name(): string;
}

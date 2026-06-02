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
 * Contract for fields. Implemented by abstract_field and all concrete field classes.
 *
 * The fluent DSL builder must be able to produce an immutable FieldDefinition
 * consumed by renderer adapters.
 *
 * @api
 */
interface FieldInterface
{
    /** Final immutable representation consumed by renderers. */
    public function toDefinition(): FieldDefinition;

    public function name(): string;
}

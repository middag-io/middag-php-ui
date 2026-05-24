<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Contract\Form;

use Middag\Ui\Data\Form\FormState;

/**
 * Contract for forms. Implemented by abstract_form (framework) and base/form (extensions).
 *
 * Covers the full lifecycle: schema declaration → hydration → validation → submission.
 * The resolver chain hydrates and validates the form before the controller executes (ADR-802).
 *
 * @api
 */
interface FormInterface
{
    /** @return array<int, FieldInterface|LayoutElementInterface> */
    public function schema(): array;

    /** Hydrate form with input data (POST/JSON). Resolver chain calls this. */
    public function hydrate(array $input): void;

    /** Run validation (schema-derived + optional form_request). */
    public function validate(): void;

    /** True when form was submitted (input not empty) AND validate() passed. */
    public function isSubmittedAndValid(): bool;

    /**
     * Validated, normalized data ready for the service layer.
     *
     * @return array<string, mixed>
     */
    public function validated(): array;

    /**
     * Per-field validation errors.
     *
     * @return array<string, string|string[]>
     */
    public function errors(): array;

    /** Underlying form state value object. */
    public function state(): FormState;
}

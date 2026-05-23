<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.com.br>
 * @copyright   2026 MIDDAG (https://www.middag.com.br)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Data\Form;

/**
 * Captures hydrated values, validation errors, and entity binding for a form lifecycle.
 *
 * Produced by abstract_form after hydration; consumed by renderers and validators.
 *
 * @internal
 */
final class FormState
{
    /** @param array<string, mixed>         $values  Hydrated input keyed by field name. */
    /** @param array<string, string|string[]> $errors  Per-field validation errors. */
    /** @param bool                         $submitted Whether the form received input. */
    public function __construct(
        private array $values = [],
        private array $errors = [],
        private bool $submitted = false,
    ) {}

    /** @return array<string, mixed> */
    public function values(): array
    {
        return $this->values;
    }

    /** @return array<string, string|string[]> */
    public function errors(): array
    {
        return $this->errors;
    }

    public function isSubmitted(): bool
    {
        return $this->submitted;
    }

    /** @param array<string, mixed> $values */
    public function withValues(array $values): self
    {
        $clone = clone $this;
        $clone->values = $values;
        $clone->submitted = true;

        return $clone;
    }

    /** @param array<string, string|string[]> $errors */
    public function withErrors(array $errors): self
    {
        $clone = clone $this;
        $clone->errors = $errors;

        return $clone;
    }
}

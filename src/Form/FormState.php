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
 * Captures hydrated values, validation errors, and submission state for a form
 * lifecycle.
 *
 * Immutable: the `with*` methods return a new instance rather than mutating.
 * A boundary object (not part of the wire contract) consumed by renderers and
 * validators, so it does not implement JsonSerializable.
 *
 * @api
 */
final readonly class FormState
{
    /**
     * @param array<string, mixed>           $values    Hydrated input keyed by field name
     * @param array<string, string|string[]> $errors    Per-field validation errors
     * @param bool                           $submitted Whether the form received input
     */
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
        return new self($values, $this->errors, true);
    }

    /** @param array<string, string|string[]> $errors */
    public function withErrors(array $errors): self
    {
        return new self($this->values, $errors, $this->submitted);
    }
}

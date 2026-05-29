<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Data;

use JsonSerializable;

/**
 * Typed client-side validation constraints for a form field.
 *
 * @api
 */
final readonly class FieldConstraints implements JsonSerializable
{
    public function __construct(
        public bool $required = false,
        public ?int $min = null,
        public ?int $max = null,
        public ?int $minLength = null,
        public ?int $maxLength = null,
        public ?string $pattern = null,
        public ?string $step = null,
    ) {}

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        $payload = [];

        if ($this->required) {
            $payload['required'] = true;
        }

        if ($this->min !== null) {
            $payload['min'] = $this->min;
        }

        if ($this->max !== null) {
            $payload['max'] = $this->max;
        }

        if ($this->minLength !== null) {
            $payload['minLength'] = $this->minLength;
        }

        if ($this->maxLength !== null) {
            $payload['maxLength'] = $this->maxLength;
        }

        if ($this->pattern !== null) {
            $payload['pattern'] = $this->pattern;
        }

        if ($this->step !== null) {
            $payload['step'] = $this->step;
        }

        return $payload;
    }
}

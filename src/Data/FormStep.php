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
 * A single step in a multi-step (wizard) form.
 *
 * `fields` may carry field names (referencing schema entries) or full
 * {@see FieldDefinition} instances; the renderer resolves them.
 *
 * @api
 */
final readonly class FormStep implements JsonSerializable
{
    /**
     * @param array<int, FieldDefinition|string> $fields
     */
    public function __construct(
        public string $id,
        public string|Translatable $label,
        public array $fields = [],
        public string|Translatable|null $help = null,
    ) {}

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        $payload = [
            'id' => $this->id,
            'label' => Label::serialize($this->label),
            'fields' => $this->fields,
        ];

        if ($this->help !== null) {
            $payload['help'] = Label::serializeNullable($this->help);
        }

        return $payload;
    }
}

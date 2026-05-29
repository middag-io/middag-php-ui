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
 * `fields` carries field names referencing schema entries; the renderer
 * resolves each name to its field. Only names (strings) are accepted: a
 * field definition is a boundary object that does not serialize to the wire,
 * so it must not be embedded here.
 *
 * @api
 */
final readonly class FormStep implements JsonSerializable
{
    /**
     * @param array<int, string> $fields Field names referencing schema entries
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

    /** @return array<string, mixed> */
    public static function jsonSchema(): array
    {
        return ['type' => 'object', 'required' => ['id', 'label', 'fields'],
            'properties' => ['id' => ['type' => 'string'], 'label' => ['$ref' => '#/$defs/Label'], 'fields' => ['type' => 'array', 'items' => ['type' => 'string']], 'help' => ['$ref' => '#/$defs/Label']],
            'additionalProperties' => false];
    }
}

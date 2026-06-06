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

use JsonSerializable;

/**
 * The wire value of a single file in a file-upload field.
 *
 * A file field's value is a `FileValue` (single) or `FileValue[]` (multiple).
 * On the wire each entry is either a bare URL string (shorthand) or an object
 * with the file's display metadata. `draftitemid` carries the Moodle draft area
 * id for a pending upload when the host round-trips it.
 *
 * Mirrors the shape @middag-io/react's FileUploadField already parses
 * (url + optional name/size/type/id), promoted to a typed wire contract so the
 * field value is no longer an opaque blob (closes the F-23 gap).
 *
 * @api
 */
final readonly class FileValue implements JsonSerializable
{
    public function __construct(
        public string $url,
        public ?string $name = null,
        public ?int $size = null,
        public ?string $type = null,
        public ?string $id = null,
        public ?int $draftitemid = null,
    ) {}

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        $payload = ['url' => $this->url];

        if ($this->name !== null) {
            $payload['name'] = $this->name;
        }

        if ($this->size !== null) {
            $payload['size'] = $this->size;
        }

        if ($this->type !== null) {
            $payload['type'] = $this->type;
        }

        if ($this->id !== null) {
            $payload['id'] = $this->id;
        }

        if ($this->draftitemid !== null) {
            $payload['draftitemid'] = $this->draftitemid;
        }

        return $payload;
    }

    /** @return array<string, mixed> */
    public static function jsonSchema(): array
    {
        return [
            'oneOf' => [
                // Shorthand: a bare URL string.
                ['type' => 'string'],
                // Full metadata object (the form PHP emits).
                [
                    'type' => 'object',
                    'properties' => [
                        'url' => ['type' => 'string'],
                        'name' => ['type' => 'string'],
                        'size' => ['type' => 'integer'],
                        'type' => ['type' => 'string'],
                        'id' => ['type' => 'string'],
                        'draftitemid' => ['type' => 'integer'],
                    ],
                    'required' => ['url'],
                    'additionalProperties' => false,
                ],
            ],
        ];
    }
}

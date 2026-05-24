<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Data\Table;

use JsonSerializable;

/**
 * Table column DTO.
 *
 * Represents a single column definition for a data grid.
 *
 * @internal
 */
readonly class Column implements JsonSerializable
{
    /**
     * @param string $key        Unique identifier for the column
     * @param string $label      Display label
     * @param bool   $sortable   Whether the column can be sorted
     * @param bool   $searchable Whether the column is searchable
     * @param string $type       Type of data (text, date, boolean, action, etc)
     * @param array  $options    Additional display options
     */
    public function __construct(
        public string $key,
        public string $label,
        public bool $sortable = false,
        public bool $searchable = false,
        public string $type = 'text',
        public array $options = []
    ) {}

    /**
     * Serialize the column definition for JSON consumers.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'key' => $this->key,
            'label' => $this->label,
            'sortable' => $this->sortable,
            'searchable' => $this->searchable,
            'type' => $this->type,
            'options' => $this->options,
        ];
    }
}

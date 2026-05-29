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
use Middag\Ui\Enum\ValueFormat;

/**
 * Table column DTO.
 *
 * Represents a single column definition for a data grid.
 *
 * @api
 */
readonly class Column implements JsonSerializable
{
    /**
     * @param string               $key           Unique identifier for the column
     * @param string|Translatable  $label         Display label (i18n intent or raw literal)
     * @param bool                 $sortable      Whether the column can be sorted
     * @param bool                 $searchable    Whether the column is searchable
     * @param ValueFormat          $format        Client-side formatting intent for the cell value
     * @param array<string, mixed> $formatOptions Format options (e.g. {currency:'BRL', decimals:2})
     * @param array<string, mixed> $options       Additional display options
     */
    public function __construct(
        public string $key,
        public string|Translatable $label,
        public bool $sortable = false,
        public bool $searchable = false,
        public ValueFormat $format = ValueFormat::TEXT,
        public array $formatOptions = [],
        public array $options = []
    ) {}

    /**
     * Serialize the column definition for JSON consumers.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        $payload = [
            'key' => $this->key,
            'label' => Label::serialize($this->label),
            'sortable' => $this->sortable,
            'searchable' => $this->searchable,
            'format' => $this->format->value,
        ];

        if ($this->formatOptions !== []) {
            $payload['formatOptions'] = $this->formatOptions;
        }

        if ($this->options !== []) {
            $payload['options'] = $this->options;
        }

        return $payload;
    }
}

<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.com.br>
 * @copyright   2026 MIDDAG (https://www.middag.com.br)
 * @license     proprietary
 */

namespace Middag\Ui\Data\Table;

use JsonSerializable;

/**
 * Table configuration DTO.
 *
 * Full definition of a table's structure and behavior.
 *
 * @internal
 */
readonly class TableConfig implements JsonSerializable
{
    /**
     * @param Column[] $columns
     * @param array    $filters List of filter definitions
     * @param array    $actions List of bulk actions or row actions
     * @param array    $options General table options (pagination, default sort, etc)
     */
    public function __construct(
        public array $columns,
        public array $filters = [],
        public array $actions = [],
        public array $options = []
    ) {}

    /**
     * Serialize the table configuration for JSON consumers.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'columns' => $this->columns,
            'filters' => $this->filters,
            'actions' => $this->actions,
            'options' => $this->options,
        ];
    }
}

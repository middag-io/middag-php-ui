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
use Middag\Ui\Contract\ActionInterface;

/**
 * Table configuration DTO.
 *
 * Full definition of a table's structure and behavior.
 *
 * @api
 */
readonly class TableConfig implements JsonSerializable
{
    /**
     * @param Column[]           $columns
     * @param FilterDefinition[] $filters
     * @param ActionInterface[]  $rowActions  Per-row actions (link/route/request targets, {id} template)
     * @param ActionInterface[]  $bulkActions Actions applied to selected rows
     * @param TableOptions       $options     General table behavior options
     */
    public function __construct(
        public array $columns,
        public array $filters = [],
        public array $rowActions = [],
        public array $bulkActions = [],
        public TableOptions $options = new TableOptions(),
    ) {}

    /**
     * Serialize the table configuration for JSON consumers.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'columns' => array_map(
                static fn (Column $column): array => $column->jsonSerialize(),
                $this->columns,
            ),
            'filters' => array_map(
                static fn (FilterDefinition $filter): array => $filter->jsonSerialize(),
                $this->filters,
            ),
            'rowActions' => array_map(
                static fn (ActionInterface $action): array => $action->jsonSerialize(),
                $this->rowActions,
            ),
            'bulkActions' => array_map(
                static fn (ActionInterface $action): array => $action->jsonSerialize(),
                $this->bulkActions,
            ),
            'options' => $this->options->jsonSerialize(),
        ];
    }
}

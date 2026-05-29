<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Builder;

use Middag\Ui\Contract\ActionInterface;
use Middag\Ui\Data\Column;
use Middag\Ui\Data\FilterDefinition;
use Middag\Ui\Data\TableConfig;
use Middag\Ui\Data\TableOptions;
use Middag\Ui\Data\Translatable;
use Middag\Ui\Enum\FilterType;
use Middag\Ui\Enum\ValueFormat;

/**
 * Table Builder.
 *
 * Fluid API to define data tables that can be consumed by client components.
 *
 * @api
 */
class TableBuilder
{
    /** @var Column[] */
    private array $columns = [];

    /** @var FilterDefinition[] */
    private array $filters = [];

    /** @var ActionInterface[] */
    private array $rowActions = [];

    /** @var ActionInterface[] */
    private array $bulkActions = [];

    private TableOptions $options;

    public function __construct()
    {
        $this->options = new TableOptions();
    }

    /**
     * Start a new table definition.
     */
    public static function make(): self
    {
        return new self();
    }

    /**
     * Add a column to the table.
     *
     * Settings keys (all optional): `sortable` (bool), `searchable` (bool),
     * `format` (ValueFormat, default TEXT), `formatOptions` (array),
     * `options` (array).
     *
     * @param array{sortable?: bool, searchable?: bool, format?: ValueFormat, formatOptions?: array<string, mixed>, options?: array<string, mixed>} $settings
     */
    public function column(string $key, string|Translatable $label, array $settings = []): self
    {
        $this->columns[] = new Column(
            key: $key,
            label: $label,
            sortable: $settings['sortable'] ?? false,
            searchable: $settings['searchable'] ?? false,
            format: $settings['format'] ?? ValueFormat::TEXT,
            formatOptions: $settings['formatOptions'] ?? [],
            options: $settings['options'] ?? [],
        );

        return $this;
    }

    /**
     * Add a typed filter definition.
     *
     * @param array<int, array{value: mixed, label: string|Translatable}> $options
     */
    public function filter(string $key, string|Translatable $label, FilterType $type = FilterType::SELECT, array $options = []): self
    {
        $this->filters[] = new FilterDefinition(
            key: $key,
            label: $label,
            type: $type,
            options: $options,
        );

        return $this;
    }

    /**
     * Add a per-row action.
     */
    public function rowAction(ActionInterface $action): self
    {
        $this->rowActions[] = $action;

        return $this;
    }

    /**
     * Add a bulk action applied to selected rows.
     */
    public function bulkAction(ActionInterface $action): self
    {
        $this->bulkActions[] = $action;

        return $this;
    }

    /**
     * Set general table behavior options.
     */
    public function options(TableOptions $options): self
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Build the final configuration DTO.
     */
    public function build(): TableConfig
    {
        return new TableConfig(
            columns: $this->columns,
            filters: $this->filters,
            rowActions: $this->rowActions,
            bulkActions: $this->bulkActions,
            options: $this->options,
        );
    }
}

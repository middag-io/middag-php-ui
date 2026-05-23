<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.com.br>
 * @copyright   2026 MIDDAG (https://www.middag.com.br)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Widget;

use Middag\Ui\Data\Table\Column as column;
use Middag\Ui\Data\Table\TableConfig;

/**
 * Table Builder.
 *
 * Fluid API to define data tables that can be consumed by client components.
 *
 * @internal
 */
class TableBuilder
{
    /** @var column[] */
    private array $columns = [];

    private array $filters = [];

    private array $actions = [];

    private array $options = [];

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
     * `type` (string, default 'text'), `options` (array).
     *
     * @param array{sortable?: bool, searchable?: bool, type?: string, options?: array} $settings
     */
    public function column(string $key, string $label, array $settings = []): self
    {
        $this->columns[] = new column(
            key: $key,
            label: $label,
            sortable: $settings['sortable'] ?? false,
            searchable: $settings['searchable'] ?? false,
            type: $settings['type'] ?? 'text',
            options: $settings['options'] ?? []
        );

        return $this;
    }

    /**
     * Add a filter definition.
     *
     * @param string $type select, text, date, etc
     */
    public function filter(string $key, string $label, string $type = 'select', array $options = []): self
    {
        $this->filters[] = [
            'key' => $key,
            'label' => $label,
            'type' => $type,
            'options' => $options,
        ];

        return $this;
    }

    /**
     * Add an action button.
     */
    public function action(string $key, string $label, string $icon = '', array $props = []): self
    {
        $this->actions[] = [
            'key' => $key,
            'label' => $label,
            'icon' => $icon,
            'props' => $props,
        ];

        return $this;
    }

    /**
     * Set general table options.
     */
    public function withOptions(array $options): self
    {
        $this->options = array_merge($this->options, $options);

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
            actions: $this->actions,
            options: $this->options
        );
    }
}

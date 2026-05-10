<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.com.br>
 * @copyright   2026 MIDDAG (https://www.middag.com.br)
 * @license     proprietary
 */

namespace Middag\Ui\Builder;

use Closure;
use InvalidArgumentException;
use Middag\Ui\Contract\CrudBuilderInterface;
use Middag\Ui\Contract\PageActionInterface;
use Middag\Ui\Data\BlockDescriptor;
use Middag\Ui\Data\PageAction;
use Middag\Ui\Data\PageContractData;

/**
 * CRUD convention builder (ADR-807 Levels 1-2).
 *
 * Generates PageContractData instances for index/create/edit/show actions
 * following convention-over-configuration. Override methods allow point-of-use
 * customization without dropping to Level 3 composable.
 *
 * @internal
 */
class CrudBuilder implements CrudBuilderInterface
{
    private readonly string $slug;

    /** @var string[] CRUD actions to generate */
    private array $actions = ['index', 'create', 'edit', 'show'];

    /** @var null|string[] Explicit columns (null = convention) */
    private ?array $columns_list = null;

    /** @var array<string, Closure> Column configurators */
    private array $column_configs = [];

    /** @var null|string[] Row actions (null = default ['edit', 'delete']) */
    private ?array $row_actions_list = null;

    /** @var null|string[] Bulk actions */
    private ?array $bulk_actions_list = null;

    /** @var null|PageActionInterface[] Page actions (null = default ['create']) */
    private ?array $page_actions_list = null;

    private int $per_page = 25;

    private string $sort_column = 'created_at';

    private string $sort_direction = 'desc';

    private ?string $custom_title = null;

    private ?string $custom_layout = null;

    private function __construct(private readonly string $entity_class)
    {
        // Derive slug from class name: Segment -> segments.
        $parts = explode('\\', $this->entity_class);
        $basename = end($parts);
        $this->slug = strtolower($basename) . 's';
    }

    public static function for(string $entity_class): self
    {
        return new self($entity_class);
    }

    // --- Level 2 Override API ---

    /**
     * Remove specific CRUD actions from generation.
     *
     * @param string ...$actions Actions to exclude (index, create, edit, show)
     */
    public function without(string ...$actions): static
    {
        $this->actions = array_values(array_diff($this->actions, $actions));

        return $this;
    }

    /**
     * Set explicit column list for index table.
     *
     * @param string[] $columns
     */
    public function columns(array $columns): static
    {
        $this->columns_list = $columns;

        return $this;
    }

    /**
     * Configure a specific column via callback.
     *
     * The callback receives the column descriptor array by reference.
     */
    public function column(string $name, Closure $configurator): static
    {
        $this->column_configs[$name] = $configurator;

        return $this;
    }

    /**
     * Set row-level actions for index table.
     *
     * @param string[] $actions
     */
    public function row_actions(array $actions): static
    {
        $this->row_actions_list = $actions;

        return $this;
    }

    /**
     * Set bulk actions for index table.
     *
     * @param string[] $actions
     */
    public function bulk_actions(array $actions): static
    {
        $this->bulk_actions_list = $actions;

        return $this;
    }

    /**
     * Set page-level actions.
     *
     * @param PageActionInterface[] $actions
     */
    public function page_actions(array $actions): static
    {
        $this->page_actions_list = $actions;

        return $this;
    }

    /**
     * Set the form class for create/edit actions.
     *
     * Reserved for future use — stores no state yet.
     */
    public function form(string $form_class): static
    {
        return $this;
    }

    /**
     * Set items per page for index table.
     */
    public function per_page(int $count): static
    {
        $this->per_page = $count;

        return $this;
    }

    /**
     * Set default sort column and direction.
     */
    public function sort(string $column, string $direction = 'desc'): static
    {
        $this->sort_column = $column;
        $this->sort_direction = $direction;

        return $this;
    }

    /**
     * Override the page title.
     */
    public function title(string $title): static
    {
        $this->custom_title = $title;

        return $this;
    }

    /**
     * Override the layout template.
     */
    public function layout(string $template): static
    {
        $this->custom_layout = $template;

        return $this;
    }

    /**
     * Set a required capability.
     *
     * Reserved for future use — stores no state yet.
     */
    public function capability(string $cap): static
    {
        return $this;
    }

    // --- Build ---

    /**
     * Build PageContractData for the given CRUD action.
     *
     * @param string $action One of: index, create, edit, show
     * @param array  $data   Context data (e.g. entity instance for edit/show, rows for index)
     */
    public function build(string $action = 'index', array $data = []): PageContractData
    {
        return match ($action) {
            'index' => $this->build_index($data),
            'create' => $this->build_create($data),
            'edit' => $this->build_edit($data),
            'show' => $this->build_show($data),
            default => throw new InvalidArgumentException('Unknown CRUD action: ' . $action),
        };
    }

    /**
     * Check if a given action is enabled.
     */
    public function has_action(string $action): bool
    {
        return in_array($action, $this->actions, true);
    }

    /**
     * Get the entity slug.
     */
    public function get_slug(): string
    {
        return $this->slug;
    }

    private function build_index(array $data): PageContractData
    {
        $title = $this->custom_title ?? ucfirst($this->slug);
        $columns = $this->columns_list ?? ['name', 'status', 'created_at'];
        $row_actions = $this->row_actions_list ?? ['edit', 'delete'];
        $page_actions = $this->page_actions_list ?? [
            new PageAction(id: 'create', label: 'Create', intent: 'primary', href: sprintf('/%s/create', $this->slug)),
        ];

        $table_data = [
            'columns' => $this->build_column_descriptors($columns),
            'rows' => $data['rows'] ?? [],
            'pagination' => $data['pagination'] ?? [
                'page' => 1,
                'perPage' => $this->per_page,
                'total' => 0,
                'lastPage' => 1,
            ],
            'sort' => [
                'column' => $this->sort_column,
                'direction' => $this->sort_direction,
            ],
            'rowActions' => array_map(
                fn (string $a): array => ['id' => $a, 'label' => ucfirst($a)],
                $row_actions,
            ),
            'bulkActions' => $this->bulk_actions_list !== null
                ? array_map(fn (string $a): array => ['id' => $a, 'label' => ucfirst($a)], $this->bulk_actions_list)
                : [],
        ];

        return PageBuilder::page($this->slug . '.index')
            ->title($title)
            ->layout($this->custom_layout ?? 'stack')
            ->actions($page_actions)
            ->region('content', [
                new BlockDescriptor(type: 'dense_table', key: $this->slug . '.table', data: $table_data),
            ])
            ->build();
    }

    private function build_create(array $data): PageContractData
    {
        $title = $this->custom_title
            ? 'Create ' . $this->custom_title
            : 'Create ' . rtrim(ucfirst($this->slug), 's');

        $form_data = [
            'action' => '/' . $this->slug,
            'method' => 'post',
            'schema' => $data['schema'] ?? [],
            'values' => $data['values'] ?? [],
            'errors' => $data['errors'] ?? [],
            'meta' => [
                'multiStep' => false,
                'cancelHref' => '/' . $this->slug,
            ],
        ];

        return PageBuilder::page($this->slug . '.create')
            ->title($title)
            ->layout($this->custom_layout ?? 'stack')
            ->region('content', [
                new BlockDescriptor(type: 'form_panel', key: $this->slug . '.form', data: $form_data),
            ])
            ->build();
    }

    private function build_edit(array $data): PageContractData
    {
        $id = $data['id'] ?? 0;
        $title = $this->custom_title
            ? 'Edit ' . $this->custom_title
            : 'Edit ' . rtrim(ucfirst($this->slug), 's');

        $form_data = [
            'action' => sprintf('/%s/%s', $this->slug, $id),
            'method' => 'put',
            'schema' => $data['schema'] ?? [],
            'values' => $data['values'] ?? [],
            'errors' => $data['errors'] ?? [],
            'meta' => [
                'multiStep' => false,
                'cancelHref' => '/' . $this->slug,
            ],
        ];

        return PageBuilder::page($this->slug . '.edit')
            ->title($title)
            ->layout($this->custom_layout ?? 'stack')
            ->region('content', [
                new BlockDescriptor(type: 'form_panel', key: $this->slug . '.form', data: $form_data),
            ])
            ->build();
    }

    private function build_show(array $data): PageContractData
    {
        $title = $this->custom_title ?? rtrim(ucfirst($this->slug), 's');

        return PageBuilder::page($this->slug . '.show')
            ->title($title)
            ->layout($this->custom_layout ?? 'split')
            ->region('content', [
                new BlockDescriptor(type: 'detail_panel', key: $this->slug . '.detail', data: $data['detail'] ?? []),
            ])
            ->region('aside', [
                new BlockDescriptor(type: 'activity_timeline', key: $this->slug . '.activity', data: $data['activity'] ?? []),
            ])
            ->build();
    }

    /**
     * @param string[] $columns
     *
     * @return array<int, array<string, mixed>>
     */
    private function build_column_descriptors(array $columns): array
    {
        return array_map(function (string $col): array {
            $descriptor = [
                'key' => $col,
                'label' => ucfirst(str_replace('_', ' ', $col)),
                'sortable' => in_array($col, ['name', 'created_at', 'updated_at', 'status'], true),
            ];

            if (isset($this->column_configs[$col])) {
                ($this->column_configs[$col])($descriptor);
            }

            return $descriptor;
        }, $columns);
    }
}

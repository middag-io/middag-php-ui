<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Region\Contract;

use Middag\Ui\Block\ChartSeries;
use Middag\Ui\Block\Contract\BlockDescriptorInterface;
use Middag\Ui\Form\Contract\FormSchemaNodeInterface;
use Middag\Ui\Form\FormStep;
use Middag\Ui\Page\Tab;
use Middag\Ui\Shared\Enum\ChartType;

/** @api */
interface RegionBuilderInterface
{
    public function metricCard(string $key, mixed $value, string $label, ?string $delta = null, ?string $icon = null, ?string $href = null): static;

    /**
     * @param array<int, mixed> $items
     */
    public function statusStrip(string $key, array $items, ?string $tone = null): static;

    /**
     * @param array<string, mixed> $data Extra data merged into the block payload
     * @param array<string, mixed> $meta Block-level meta (e.g. `clientSide` to
     *                                   filter/sort/paginate in the browser)
     */
    public function denseTable(string $key, array $columns, array $rows = [], array $data = [], array $meta = []): static;

    /**
     * @param array<string, mixed> $data Extra data merged into the block payload
     */
    public function detailPanel(string $key, array $sections, array $data = []): static;

    public function activityTimeline(string $key, array $groups, bool $hasMore = false, ?string $loadMoreHref = null): static;

    public function emptyState(string $key, string $variant = 'first-use', ?string $description = null, ?string $cta = null, ?string $icon = null): static;

    /**
     * @param array<int, array<string, mixed>|FormSchemaNodeInterface> $schema Form schema nodes (VO or legacy loose array)
     * @param array<string, mixed>                                     $values
     * @param null|array<int, FormStep>                                $steps  Wizard steps; when set, the form renders multi-step
     * @param array<string, mixed>                                     $data   Extra data merged into the block payload
     */
    public function formPanel(string $key, string $action, string $method = 'POST', array $schema = [], array $values = [], ?array $steps = null, array $data = []): static;

    public function markdownPanel(string $key, string $content, ?int $maxHeight = null): static;

    /**
     * @param array<string, mixed> $data Extra data merged into the block payload
     */
    public function cardGrid(string $key, array $columns, array $rows = [], ?string $variant = null, array $data = []): static;

    /**
     * @param array<int, array{id: string, icon: string, title: string, description: string, actionUrl: string, actionMethod?: string, confirmText?: string}> $items
     * @param null|array{success: bool, message: string}                                                                                                      $flash
     */
    public function actionGrid(string $key, array $items, ?array $flash = null): static;

    /**
     * @param array<int, array{label: string, href: null|string, icon?: string, description?: string, external?: bool}> $items
     */
    public function linkList(string $key, array $items): static;

    /**
     * @param ChartSeries[]                    $series  one entry per plotted series
     * @param array<int, array<string, mixed>> $data    chart rows; each row has the
     *                                                  categoryKey field plus one field per series key
     * @param array<string, mixed>             $options extra display options (showLegend, showGrid, …)
     */
    public function chart(string $key, ChartType $type, array $series, string $categoryKey, array $data, array $options = []): static;

    /**
     * @param Tab[] $tabs
     */
    public function tabs(string $key, array $tabs, ?string $defaultTab = null): static;

    /**
     * @param array<string, mixed> $data Block payload
     */
    public function block(string $type, string $key, array $data = []): static;

    /**
     * @return BlockDescriptorInterface[]
     */
    public function all(): array;
}

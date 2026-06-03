<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Region;

use Middag\Ui\Block\BlockBuilder;
use Middag\Ui\Block\BlockDescriptor;
use Middag\Ui\Block\ChartSeries;
use Middag\Ui\Block\Contract\BlockDescriptorInterface;
use Middag\Ui\Form\FormStep;
use Middag\Ui\Page\PageBuilder;
use Middag\Ui\Page\Tab;
use Middag\Ui\Region\Contract\RegionBuilderInterface;
use Middag\Ui\Shared\Enum\ChartType;

/**
 * Fluent builder for composing blocks within a layout region.
 *
 * Provides shorthand methods for common block types. Used via callback
 * in PageBuilder::region().
 *
 * @api
 */
class RegionBuilder implements RegionBuilderInterface
{
    /** @var BlockDescriptorInterface[] */
    private array $blocks = [];

    /**
     * Add a metric card block.
     */
    public function metricCard(string $key, mixed $value, string $label, ?string $delta = null, ?string $icon = null, ?string $href = null): static
    {
        $this->blocks[] = BlockBuilder::metricCard($key, $value, $label, $delta, $icon, $href);

        return $this;
    }

    /**
     * Add a status strip block.
     *
     * @param array<int, mixed> $items
     */
    public function statusStrip(string $key, array $items, ?string $tone = null): static
    {
        $this->blocks[] = BlockBuilder::statusStrip($key, $items, $tone);

        return $this;
    }

    /**
     * Add a dense table block.
     *
     * @param array<string, mixed> $data Extra data merged into the block payload
     * @param array<string, mixed> $meta Block-level meta (e.g. `clientSide` to
     *                                   filter/sort/paginate in the browser)
     */
    public function denseTable(string $key, array $columns, array $rows = [], array $data = [], array $meta = []): static
    {
        $this->blocks[] = BlockBuilder::denseTable($key, $columns, $rows, $data, $meta);

        return $this;
    }

    /**
     * Add a detail panel block.
     *
     * @param array<string, mixed> $data Extra data merged into the block payload
     */
    public function detailPanel(string $key, array $sections, array $data = []): static
    {
        $this->blocks[] = BlockBuilder::detailPanel($key, $sections, $data);

        return $this;
    }

    /**
     * Add an activity timeline block.
     */
    public function activityTimeline(string $key, array $groups, bool $hasMore = false, ?string $loadMoreHref = null): static
    {
        $this->blocks[] = BlockBuilder::activityTimeline($key, $groups, $hasMore, $loadMoreHref);

        return $this;
    }

    /**
     * Add an empty state block.
     */
    public function emptyState(string $key, string $variant = 'first-use', ?string $description = null, ?string $cta = null, ?string $icon = null): static
    {
        $this->blocks[] = BlockBuilder::emptyState($key, $variant, $description, $cta, $icon);

        return $this;
    }

    /**
     * Add a form panel block.
     *
     * @param array<string, mixed>      $schema
     * @param array<string, mixed>      $values
     * @param null|array<int, FormStep> $steps  Wizard steps; when set, the form renders multi-step
     * @param array<string, mixed>      $data   Extra data merged into the block payload
     */
    public function formPanel(string $key, string $action, string $method = 'POST', array $schema = [], array $values = [], ?array $steps = null, array $data = []): static
    {
        $this->blocks[] = BlockBuilder::formPanel($key, $action, $method, $schema, $values, $steps, $data);

        return $this;
    }

    /**
     * Add a markdown panel block.
     */
    public function markdownPanel(string $key, string $content, ?int $maxHeight = null): static
    {
        $this->blocks[] = BlockBuilder::markdownPanel($key, $content, $maxHeight);

        return $this;
    }

    /**
     * Add a card grid block.
     *
     * @param array<string, mixed> $data Extra data merged into the block payload
     */
    public function cardGrid(string $key, array $columns, array $rows = [], ?string $variant = null, array $data = []): static
    {
        $this->blocks[] = BlockBuilder::cardGrid($key, $columns, $rows, $variant, $data);

        return $this;
    }

    /**
     * Add an action grid block.
     *
     * @param array<int, array{id: string, icon: string, title: string, description: string, actionUrl: string, actionMethod?: string, confirmText?: string}> $items
     * @param null|array{success: bool, message: string}                                                                                                      $flash
     */
    public function actionGrid(string $key, array $items, ?array $flash = null): static
    {
        $this->blocks[] = BlockBuilder::actionGrid($key, $items, $flash);

        return $this;
    }

    /**
     * Add a link list block.
     *
     * @param array<int, array{label: string, href: null|string, icon?: string, description?: string, external?: bool}> $items
     */
    public function linkList(string $key, array $items): static
    {
        $this->blocks[] = BlockBuilder::linkList($key, $items);

        return $this;
    }

    /**
     * Add a chart block.
     *
     * @param ChartSeries[]        $series
     * @param array<int, mixed>    $categories
     * @param array<string, mixed> $options
     */
    public function chart(string $key, ChartType $type, array $series, array $categories = [], array $options = []): static
    {
        $this->blocks[] = BlockBuilder::chart($key, $type, $series, $categories, $options);

        return $this;
    }

    /**
     * Add a tabs container block.
     *
     * @param Tab[] $tabs
     */
    public function tabs(string $key, array $tabs): static
    {
        $this->blocks[] = BlockBuilder::tabs($key, $tabs);

        return $this;
    }

    /**
     * Add a generic block of any type.
     *
     * Use this for custom or less common block types not covered
     * by the dedicated shorthand methods.
     *
     * @param array<string, mixed> $data Block payload
     */
    public function block(string $type, string $key, array $data = []): static
    {
        $this->blocks[] = new BlockDescriptor(
            type: $type,
            key: $key,
            data: $data,
        );

        return $this;
    }

    /**
     * Return all collected block descriptors.
     *
     * @return BlockDescriptorInterface[]
     */
    public function all(): array
    {
        return $this->blocks;
    }
}

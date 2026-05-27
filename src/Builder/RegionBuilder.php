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

use Middag\Ui\Contract\BlockDescriptorInterface;
use Middag\Ui\Contract\RegionBuilderInterface;
use Middag\Ui\Data\BlockDescriptor;

/**
 * Fluent builder for composing blocks within a layout region (ADR-807).
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
     *
     * @param array<string, mixed> $data Additional data merged with title
     */
    public function metricCard(string $key, string $title = '', array $data = []): static
    {
        $this->blocks[] = new BlockDescriptor(
            type: 'metric_card',
            key: $key,
            data: array_merge(['title' => $title], $data),
        );

        return $this;
    }

    /**
     * Add a status strip block.
     *
     * @param array<string, mixed> $data Block payload
     */
    public function statusStrip(string $key, array $data = []): static
    {
        $this->blocks[] = new BlockDescriptor(
            type: 'status_strip',
            key: $key,
            data: $data,
        );

        return $this;
    }

    /**
     * Add a dense table block.
     *
     * @param array<string, mixed> $data Additional data merged with title
     */
    public function denseTable(string $key, string $title = '', array $data = []): static
    {
        $this->blocks[] = new BlockDescriptor(
            type: 'dense_table',
            key: $key,
            data: array_merge(['title' => $title], $data),
        );

        return $this;
    }

    /**
     * Add a detail panel block.
     *
     * @param array<string, mixed> $data Block payload
     */
    public function detailPanel(string $key, array $data = []): static
    {
        $this->blocks[] = new BlockDescriptor(
            type: 'detail_panel',
            key: $key,
            data: $data,
        );

        return $this;
    }

    /**
     * Add an activity timeline block.
     *
     * @param array<string, mixed> $data Additional data merged with title
     */
    public function activityTimeline(string $key, string $title = '', array $data = []): static
    {
        $this->blocks[] = new BlockDescriptor(
            type: 'activity_timeline',
            key: $key,
            data: array_merge(['title' => $title], $data),
        );

        return $this;
    }

    /**
     * Add an empty state block.
     *
     * @param array<string, mixed> $data Block payload (title, description, action, variant)
     */
    public function emptyState(string $key, array $data = []): static
    {
        $this->blocks[] = new BlockDescriptor(
            type: 'empty_state',
            key: $key,
            data: $data,
        );

        return $this;
    }

    /**
     * Add a form panel block.
     *
     * @param array<string, mixed> $data Block payload
     */
    public function formPanel(string $key, string $title = '', array $data = []): static
    {
        $this->blocks[] = new BlockDescriptor(
            type: 'form_panel',
            key: $key,
            data: array_merge(['title' => $title], $data),
        );

        return $this;
    }

    /**
     * Add a markdown panel block.
     */
    public function markdownPanel(string $key, string $content = ''): static
    {
        $this->blocks[] = new BlockDescriptor(
            type: 'markdown_panel',
            key: $key,
            data: ['content' => $content],
        );

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

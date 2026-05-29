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

use Middag\Ui\Data\BlockDescriptor;

/**
 * Static factory for creating BlockDescriptor instances (ADR-807).
 *
 * Provides named constructors for each standard block type,
 * avoiding the need to remember block type strings and data shapes.
 *
 * @api
 */
class BlockBuilder
{
    /**
     * @param array<string, mixed> $data Extra data merged into the block payload
     */
    public static function denseTable(string $key, array $columns, array $rows = [], array $data = []): BlockDescriptor
    {
        return new BlockDescriptor(
            type: 'dense_table',
            key: $key,
            data: array_merge(['columns' => $columns, 'rows' => $rows], $data),
        );
    }

    /**
     * @param array<string, mixed> $data Extra data merged into the block payload
     */
    public static function formPanel(string $key, string $action, string $method = 'POST', array $schema = [], array $values = [], array $data = []): BlockDescriptor
    {
        return new BlockDescriptor(
            type: 'form_panel',
            key: $key,
            data: array_merge(['action' => $action, 'method' => $method, 'schema' => $schema, 'values' => $values], $data),
        );
    }

    public static function detailPanel(string $key, array $sections, array $data = []): BlockDescriptor
    {
        return new BlockDescriptor(
            type: 'detail_panel',
            key: $key,
            data: array_merge(['sections' => $sections], $data),
        );
    }

    public static function metricCard(string $key, mixed $value, string $label, ?string $delta = null, ?string $icon = null, ?string $href = null): BlockDescriptor
    {
        return new BlockDescriptor(
            type: 'metric_card',
            key: $key,
            data: array_filter(['value' => $value, 'label' => $label, 'delta' => $delta, 'icon' => $icon, 'href' => $href], static fn ($v): bool => $v !== null),
        );
    }

    public static function emptyState(string $key, string $variant = 'first-use', ?string $description = null, ?string $cta = null, ?string $icon = null): BlockDescriptor
    {
        return new BlockDescriptor(
            type: 'empty_state',
            key: $key,
            data: array_filter(['variant' => $variant, 'description' => $description, 'cta' => $cta, 'icon' => $icon], static fn (?string $v): bool => $v !== null),
        );
    }

    public static function statusStrip(string $key, array $items, ?string $tone = null): BlockDescriptor
    {
        return new BlockDescriptor(
            type: 'status_strip',
            key: $key,
            data: array_filter(['items' => $items, 'tone' => $tone], static fn (array|string|null $v): bool => $v !== null),
        );
    }

    public static function activityTimeline(string $key, array $groups, bool $has_more = false, ?string $load_more_href = null): BlockDescriptor
    {
        return new BlockDescriptor(
            type: 'activity_timeline',
            key: $key,
            data: array_filter(['groups' => $groups, 'has_more' => $has_more, 'load_more_href' => $load_more_href], static fn (array|bool|string|null $v): bool => $v !== null),
        );
    }

    public static function markdownPanel(string $key, string $content, ?int $max_height = null): BlockDescriptor
    {
        return new BlockDescriptor(
            type: 'markdown_panel',
            key: $key,
            data: array_filter(['content' => $content, 'max_height' => $max_height], static fn (int|string|null $v): bool => $v !== null),
        );
    }

    /**
     * @param array<string, mixed> $data Extra data merged into the block payload
     */
    public static function cardGrid(string $key, array $columns, array $rows = [], ?string $variant = null, array $data = []): BlockDescriptor
    {
        return new BlockDescriptor(
            type: 'card_grid',
            key: $key,
            data: array_merge(
                ['columns' => $columns, 'rows' => $rows],
                $variant !== null ? ['variant' => $variant] : [],
                $data,
            ),
        );
    }

    /**
     * @param array<int, array{id: string, icon: string, title: string, description: string, actionUrl: string, actionMethod?: string, confirmText?: string}> $items
     * @param null|array{success: bool, message: string}                                                                                                      $flash
     */
    public static function actionGrid(string $key, array $items, ?array $flash = null): BlockDescriptor
    {
        return new BlockDescriptor(
            type: 'action_grid',
            key: $key,
            data: array_filter(['items' => $items, 'flash' => $flash], static fn (?array $v): bool => $v !== null),
        );
    }

    /**
     * @param array<int, array{label: string, href: null|string, icon?: string, description?: string, external?: bool}> $items
     */
    public static function linkList(string $key, array $items): BlockDescriptor
    {
        return new BlockDescriptor(type: 'link_list', key: $key, data: ['items' => $items]);
    }
}

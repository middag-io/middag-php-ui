<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Block;

use Middag\Ui\Form\Contract\FormSchemaNodeInterface;
use Middag\Ui\Form\FormStep;
use Middag\Ui\Page\Tab;
use Middag\Ui\Shared\Enum\ChartType;

/**
 * Static factory for creating BlockDescriptor instances.
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
     * @param array<string, mixed> $meta Block-level meta the React DenseTableBlock
     *                                   reads off `block.meta` (NOT `data`): e.g.
     *                                   `clientSide` (filter/sort/paginate in the
     *                                   browser on the supplied rows),
     *                                   `partialReloadKey`, `rowKey`, `loading`.
     */
    public static function denseTable(string $key, array $columns, array $rows = [], array $data = [], array $meta = []): BlockDescriptor
    {
        return new BlockDescriptor(
            type: 'dense_table',
            key: $key,
            data: array_merge(['columns' => $columns, 'rows' => $rows], $data),
            meta: $meta,
        );
    }

    /**
     * @param array<int, array<string, mixed>|FormSchemaNodeInterface> $schema Form schema nodes.
     *                                                                         Dual-accept during the form-on-wire migration: each element
     *                                                                         is either a {@see FormSchemaNodeInterface} VO (serialized here
     *                                                                         via jsonSerialize) or a legacy loose array (passed through
     *                                                                         untouched). Loose passthrough is removed once every producer
     *                                                                         builds VOs (Phase 4).
     * @param array<string, mixed>                                     $values
     * @param null|array<int, FormStep>                                $steps  Wizard steps; when set, the form renders multi-step
     * @param array<string, mixed>                                     $data   Extra data merged into the block payload
     */
    public static function formPanel(string $key, string $action, string $method = 'POST', array $schema = [], array $values = [], ?array $steps = null, array $data = []): BlockDescriptor
    {
        return new BlockDescriptor(
            type: 'form_panel',
            key: $key,
            data: array_merge(
                [
                    'action' => $action,
                    // FormMethod on the wire is lowercase (post|put|patch); the
                    // @middag-io/react FormPanelBlock matches `data.method === "put"`
                    // exactly, so an uppercase "PUT" silently falls back to POST.
                    'method' => strtolower($method),
                    'schema' => array_map(
                        static fn (mixed $node): mixed => $node instanceof FormSchemaNodeInterface ? $node->jsonSerialize() : $node,
                        $schema,
                    ),
                    'values' => $values,
                    // The @middag-io/react FormPanelBlock requires `errors` and
                    // `meta` (it reads `data.meta.validation` and iterates
                    // `data.errors`). Default them so a minimal form_panel payload
                    // renders; callers override via $data (server validation errors,
                    // validation mode, submit/cancel labels).
                    'errors' => (object) [],
                    'meta' => ['validation' => 'both'],
                ],
                $steps !== null ? ['steps' => $steps, 'multiStep' => true] : [],
                $data,
            ),
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

    /**
     * Build an activity-timeline block.
     *
     * `$groups` is a list of `['label' => string, 'entries' => Entry[]]`. Each
     * entry is a loose array (not modelled by a VO — FREE block-data stays loose
     * on the wire) with the shape:
     *   `id, actor, action, icon, color, timestamp` (required) and the optional
     *   `actorHref, detail`. To surface a per-entry read/acknowledge affordance
     *   (the client renders a "mark as read" action), add the optional pair:
     *   `markReadHref` (string — the endpoint the client POSTs to) and `read`
     *   (bool — current acknowledged state). Both are additive and passthrough;
     *   entries without them render unchanged.
     */
    public static function activityTimeline(string $key, array $groups, bool $hasMore = false, ?string $loadMoreHref = null): BlockDescriptor
    {
        return new BlockDescriptor(
            type: 'activity_timeline',
            key: $key,
            data: array_filter(['groups' => $groups, 'hasMore' => $hasMore, 'loadMoreHref' => $loadMoreHref], static fn (array|bool|string|null $v): bool => $v !== null),
        );
    }

    /**
     * Build a Markdown panel block.
     *
     * SECURITY: `$content` is rendered as HTML by the client renderer. Callers
     * MUST NOT pass untrusted input unless the renderer sanitizes it — this
     * builder is a transport-agnostic serializer and does not escape markup.
     */
    public static function markdownPanel(string $key, string $content, ?int $maxHeight = null): BlockDescriptor
    {
        return new BlockDescriptor(
            type: 'markdown_panel',
            key: $key,
            data: array_filter(['content' => $content, 'maxHeight' => $maxHeight], static fn (int|string|null $v): bool => $v !== null),
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

    /**
     * @param ChartSeries[]        $series
     * @param array<int, mixed>    $categories
     * @param array<string, mixed> $options
     */
    public static function chart(string $key, ChartType $type, array $series, array $categories = [], array $options = []): BlockDescriptor
    {
        return new BlockDescriptor(
            type: 'chart',
            key: $key,
            data: array_merge(
                [
                    'chartType' => $type->value,
                    'series' => array_map(
                        static fn (ChartSeries $serie): array => $serie->jsonSerialize(),
                        $series,
                    ),
                ],
                $categories !== [] ? ['categories' => $categories] : [],
                $options !== [] ? ['options' => $options] : [],
            ),
        );
    }

    /**
     * @param Tab[] $tabs
     */
    public static function tabs(string $key, array $tabs): BlockDescriptor
    {
        return new BlockDescriptor(
            type: 'tabs',
            key: $key,
            data: [
                'tabs' => array_map(
                    static fn (Tab $tab): array => $tab->jsonSerialize(),
                    $tabs,
                ),
            ],
        );
    }
}

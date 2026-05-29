<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Contract;

/** @api */
interface RegionBuilderInterface
{
    public function metricCard(string $key, mixed $value, string $label, ?string $delta = null, ?string $icon = null, ?string $href = null): static;

    public function statusStrip(string $key, array $items, ?string $tone = null): static;

    public function denseTable(string $key, array $columns, array $rows = [], array $data = []): static;

    public function detailPanel(string $key, array $sections, array $data = []): static;

    public function activityTimeline(string $key, array $groups, bool $hasMore = false, ?string $loadMoreHref = null): static;

    public function emptyState(string $key, string $variant = 'first-use', ?string $description = null, ?string $cta = null, ?string $icon = null): static;

    public function formPanel(string $key, string $action, string $method = 'POST', array $schema = [], array $values = [], ?array $steps = null, array $data = []): static;

    public function markdownPanel(string $key, string $content, ?int $maxHeight = null): static;

    public function cardGrid(string $key, array $columns, array $rows = [], ?string $variant = null, array $data = []): static;

    public function actionGrid(string $key, array $items, ?array $flash = null): static;

    public function linkList(string $key, array $items): static;

    public function block(string $type, string $key, array $data = []): static;

    public function all(): array;
}

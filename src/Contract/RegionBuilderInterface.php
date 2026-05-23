<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.com.br>
 * @copyright   2026 MIDDAG (https://www.middag.com.br)
 * @license     proprietary
 */

namespace Middag\Ui\Contract;

/** @api */
interface RegionBuilderInterface
{
    public function metricCard(string $key, string $title = '', array $data = []): static;

    public function statusStrip(string $key, array $data = []): static;

    public function denseTable(string $key, string $title = '', array $data = []): static;

    public function detailPanel(string $key, array $data = []): static;

    public function activityTimeline(string $key, string $title = '', array $data = []): static;

    public function emptyState(string $key, array $data = []): static;

    public function formPanel(string $key, string $title = '', array $data = []): static;

    public function markdownPanel(string $key, string $content = ''): static;

    public function block(string $type, string $key, array $data = []): static;

    public function all(): array;
}

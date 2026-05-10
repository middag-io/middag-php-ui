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
    public function metric_card(string $key, string $title = '', array $data = []): static;

    public function status_strip(string $key, array $data = []): static;

    public function dense_table(string $key, string $title = '', array $data = []): static;

    public function detail_panel(string $key, array $data = []): static;

    public function activity_timeline(string $key, string $title = '', array $data = []): static;

    public function empty_state(string $key, array $data = []): static;

    public function form_panel(string $key, string $title = '', array $data = []): static;

    public function markdown_panel(string $key, string $content = ''): static;

    public function block(string $type, string $key, array $data = []): static;

    public function all(): array;
}

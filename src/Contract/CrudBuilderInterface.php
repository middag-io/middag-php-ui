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

use Closure;

/** @api */
interface CrudBuilderInterface
{
    public function without(string ...$actions): static;

    public function columns(array $columns): static;

    public function column(string $name, Closure $configurator): static;

    public function row_actions(array $actions): static;

    public function bulk_actions(array $actions): static;

    public function page_actions(array $actions): static;

    public function form(string $form_class): static;

    public function per_page(int $count): static;

    public function sort(string $column, string $direction = 'desc'): static;

    public function title(string $title): static;

    public function layout(string $template): static;

    public function capability(string $cap): static;

    public function build(string $action = 'index', array $data = []): PageContractInterface;

    public function has_action(string $action): bool;

    public function get_slug(): string;
}

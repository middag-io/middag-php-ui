<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.com.br>
 * @copyright   2026 MIDDAG (https://www.middag.com.br)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Contract;

use Closure;

/** @api */
interface CrudBuilderInterface
{
    public function without(string ...$actions): static;

    public function columns(array $columns): static;

    public function column(string $name, Closure $configurator): static;

    public function rowActions(array $actions): static;

    public function bulkActions(array $actions): static;

    public function pageActions(array $actions): static;

    public function form(string $form_class): static;

    public function perPage(int $count): static;

    public function sort(string $column, string $direction = 'desc'): static;

    public function title(string $title): static;

    public function layout(string $template): static;

    public function capability(string $cap): static;

    public function build(string $action = 'index', array $data = []): PageContractInterface;

    public function hasAction(string $action): bool;

    public function getSlug(): string;
}

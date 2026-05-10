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
interface PageBuilderInterface
{
    public function title(string $title): static;

    public function subtitle(string $subtitle): static;

    public function shell(string $shell): static;

    public function layout(string $template): static;

    public function region(string $name, array|Closure $blocks): static;

    public function breadcrumbs(Closure $callback): static;

    public function actions(array $actions): static;

    public function overlay(): static;

    public function help(string $title, string $body, ?string $learn_more = null): static;

    public function inspector(string $endpoint, int $width = 440): static;

    public function build(): PageContractInterface;

    public function to_props(): array;
}

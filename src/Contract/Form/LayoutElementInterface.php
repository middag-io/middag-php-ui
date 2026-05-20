<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.com.br>
 * @copyright   2026 MIDDAG (https://www.middag.com.br)
 * @license     proprietary
 */

namespace Middag\Ui\Contract\Form;

/**
 * Layout primitive (section, group). Wraps a list of fields or nested elements.
 *
 * Implemented by section and group in infrastructure/form/layout/.
 * Allows renderers to traverse the schema tree uniformly (ADR-806).
 *
 * @api
 */
interface LayoutElementInterface
{
    public function id(): string;

    /** @return array<int, FieldInterface|self> */
    public function children(): array;
}

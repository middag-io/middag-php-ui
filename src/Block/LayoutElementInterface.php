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

use Middag\Ui\Form\FieldInterface;

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

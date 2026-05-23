<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.com.br>
 * @copyright   2026 MIDDAG (https://www.middag.com.br)
 * @license     proprietary
 */

namespace Middag\Ui;

use Middag\Ui\Contract\WidgetInterface;

/**
 * Abstract base widget.
 *
 * Base contract for UI widgets discovered by the application layer.
 * Concrete widgets should extend the public SDK wrapper instead of this
 * internal base class.
 *
 * @see WidgetInterface
 *
 * @internal
 */
abstract class AbstractWidget implements WidgetInterface {}

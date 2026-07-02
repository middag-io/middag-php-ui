<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Form\Contract;

use Middag\Ui\Shared\Enum\RenderTarget;
use Middag\Ui\Shared\ValueObject\RendererOutput;

/**
 * Adapter contract — produces a RendererOutput for one RenderTarget.
 *
 * Adapter implementations are registered with the host renderer registry,
 * which selects the correct adapter at render time.
 *
 * @api
 */
interface FormRendererInterface
{
    /** Identity token used to route form rendering to the matching adapter. */
    public static function target(): RenderTarget;

    public function render(FormInterface $form): RendererOutput;
}

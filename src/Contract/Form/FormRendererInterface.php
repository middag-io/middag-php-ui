<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Contract\Form;

use Middag\Ui\Data\Form\RendererOutput;
use Middag\Ui\Enum\RenderTarget;

/**
 * Adapter contract — produces a RendererOutput for one RenderTarget (ADR-805).
 *
 * Host adapter implementations are registered in the renderer_registry,
 * which selects the correct adapter at render time.
 *
 * @api
 */
interface FormRendererInterface
{
    /** Identity token used by renderer_registry to route form rendering. */
    public static function target(): RenderTarget;

    public function render(FormInterface $form): RendererOutput;
}

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

use Middag\Ui\Data\Form\RendererOutput as renderer_output;
use Middag\Ui\Enum\RenderTarget as render_target;

/**
 * Adapter contract — produces a renderer_output for one render_target (ADR-805).
 *
 * Implementations (mform_renderer, inertia_renderer) are registered in the
 * renderer_registry, which selects the correct adapter at render time.
 *
 * @internal
 */
interface FormRendererInterface
{
    /** Identity token used by renderer_registry to route form rendering. */
    public static function target(): render_target;

    public function render(FormInterface $form): renderer_output;
}

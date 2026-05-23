<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.com.br>
 * @copyright   2026 MIDDAG (https://www.middag.com.br)
 * @license     proprietary
 */

namespace Middag\Ui\Data\Form;

use Middag\Ui\Enum\RenderTarget as render_target;

/**
 * Output of a form renderer. Carries either HTML body (mform) or props (inertia).
 *
 * Produced exclusively by form_renderer_interface implementations.
 * Consumed by abstract_controller::render_form() to produce the HTTP response.
 *
 * @internal
 */
final readonly class RendererOutput
{
    /** @param array<string, mixed> $props */
    private function __construct(
        public render_target $target,
        public string $body,
        public array $props,
    ) {}

    /** Build an HTML-body output (mform target). */
    public static function html(render_target $target, string $body): self
    {
        return new self($target, $body, []);
    }

    /**
     * Build a props output (inertia target).
     *
     * @param array<string, mixed> $props
     */
    public static function props(render_target $target, array $props): self
    {
        return new self($target, '', $props);
    }
}

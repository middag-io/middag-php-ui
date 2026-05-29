<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Shared\Data;

use Middag\Ui\Shared\Enum\RenderTarget;

/**
 * Output of a form renderer. Carries either an HTML body or structured props.
 *
 * Produced by FormRendererInterface implementations and consumed by the host
 * controller layer to produce its response.
 *
 * @api
 */
final readonly class RendererOutput
{
    /** @param array<string, mixed> $props */
    private function __construct(
        public RenderTarget $target,
        public string $body,
        public array $props,
    ) {}

    /** Build an HTML-body output. */
    public static function html(RenderTarget $target, string $body): self
    {
        return new self($target, $body, []);
    }

    /**
     * Build a structured-props output.
     *
     * @param array<string, mixed> $props
     */
    public static function props(RenderTarget $target, array $props): self
    {
        return new self($target, '', $props);
    }
}

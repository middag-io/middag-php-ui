<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Tests\Data;

use Middag\Ui\Data\RendererOutput;
use Middag\Ui\Enum\RenderTarget;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(RendererOutput::class)]
final class RendererOutputTest extends TestCase
{
    #[Test]
    public function testHtmlFactorySetstargetAndBody(): void
    {
        $output = RendererOutput::html(RenderTarget::HTML, '<form>...</form>');

        self::assertSame(RenderTarget::HTML, $output->target);
        self::assertSame('<form>...</form>', $output->body);
    }

    #[Test]
    public function testHtmlFactoryHasEmptyProps(): void
    {
        $output = RendererOutput::html(RenderTarget::HTML, '<form/>');

        self::assertSame([], $output->props);
    }

    #[Test]
    public function testPropsFactorySetsTargetAndProps(): void
    {
        $props = ['fields' => ['name', 'email'], 'values' => []];
        $output = RendererOutput::props(RenderTarget::PROPS, $props);

        self::assertSame(RenderTarget::PROPS, $output->target);
        self::assertSame($props, $output->props);
    }

    #[Test]
    public function testPropsFactoryHasEmptyBody(): void
    {
        $output = RendererOutput::props(RenderTarget::PROPS, []);

        self::assertSame('', $output->body);
    }

    #[Test]
    public function testHtmlAndPropsUseDifferentTargets(): void
    {
        $html = RendererOutput::html(RenderTarget::HTML, '<form/>');
        $props = RendererOutput::props(RenderTarget::PROPS, []);

        self::assertSame(RenderTarget::HTML, $html->target);
        self::assertSame(RenderTarget::PROPS, $props->target);
    }
}

<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Tests\Shared\ValueObject;

use Middag\Ui\Shared\Enum\RenderTarget;
use Middag\Ui\Shared\ValueObject\RendererOutput;
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
        $output = RendererOutput::html(RenderTarget::Html, '<form>...</form>');

        self::assertSame(RenderTarget::Html, $output->target);
        self::assertSame('<form>...</form>', $output->body);
    }

    #[Test]
    public function testHtmlFactoryHasEmptyProps(): void
    {
        $output = RendererOutput::html(RenderTarget::Html, '<form/>');

        self::assertSame([], $output->props);
    }

    #[Test]
    public function testPropsFactorySetsTargetAndProps(): void
    {
        $props = ['fields' => ['name', 'email'], 'values' => []];
        $output = RendererOutput::props(RenderTarget::Props, $props);

        self::assertSame(RenderTarget::Props, $output->target);
        self::assertSame($props, $output->props);
    }

    #[Test]
    public function testPropsFactoryHasEmptyBody(): void
    {
        $output = RendererOutput::props(RenderTarget::Props, []);

        self::assertSame('', $output->body);
    }

    #[Test]
    public function testHtmlAndPropsUseDifferentTargets(): void
    {
        $html = RendererOutput::html(RenderTarget::Html, '<form/>');
        $props = RendererOutput::props(RenderTarget::Props, []);

        self::assertSame(RenderTarget::Html, $html->target);
        self::assertSame(RenderTarget::Props, $props->target);
    }
}

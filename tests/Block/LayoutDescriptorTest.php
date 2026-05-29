<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Tests\Block;

use Middag\Ui\Block\BlockDescriptor;
use Middag\Ui\Block\LayoutDescriptor;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(LayoutDescriptor::class)]
final class LayoutDescriptorTest extends TestCase
{
    #[Test]
    public function testSerializesTemplateAndRegions(): void
    {
        $block = new BlockDescriptor(
            type: 'dense_table',
            key: 'users',
            data: ['columns' => []],
        );

        $layout = new LayoutDescriptor(
            template: 'stack',
            regions: ['main' => [$block]],
        );

        $payload = $layout->jsonSerialize();

        self::assertSame('stack', $payload['template']);
        self::assertArrayHasKey('main', $payload['regions']);
        self::assertCount(1, $payload['regions']['main']);
        self::assertSame('dense_table', $payload['regions']['main'][0]['type']);
        self::assertSame('users', $payload['regions']['main'][0]['key']);
    }

    #[Test]
    public function testOmitsMetaWhenEmpty(): void
    {
        $layout = new LayoutDescriptor(
            template: 'stack',
            regions: ['main' => []],
            meta: [],
        );

        $payload = $layout->jsonSerialize();

        self::assertArrayNotHasKey('meta', $payload);
    }

    #[Test]
    public function testIncludesMetaWhenSet(): void
    {
        $block = new BlockDescriptor(
            type: 'dense_table',
            key: 'users',
            data: [],
        );

        $layout = new LayoutDescriptor(
            template: 'wizard',
            regions: ['main' => [$block]],
            meta: ['steps' => 3],
        );

        $payload = $layout->jsonSerialize();

        self::assertArrayHasKey('meta', $payload);
        self::assertSame(['steps' => 3], $payload['meta']);
    }
}

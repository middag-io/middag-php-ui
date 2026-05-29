<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Tests\Navigation;

use Middag\Ui\Navigation\BreadcrumbInterface;
use Middag\Ui\Navigation\BreadcrumbListBuilder;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(BreadcrumbListBuilder::class)]
final class BreadcrumbListBuilderTest extends TestCase
{
    #[Test]
    public function testEmptyList(): void
    {
        $builder = new BreadcrumbListBuilder();

        self::assertSame([], $builder->all());
    }

    #[Test]
    public function testItemAddsBreadcrumbWithHref(): void
    {
        $builder = new BreadcrumbListBuilder();
        $builder->item('Home', '/');

        $items = $builder->all();

        self::assertCount(1, $items);

        $payload = $items[0]->jsonSerialize();

        self::assertSame('Home', $payload['label']);
        self::assertSame('/', $payload['href']);
    }

    #[Test]
    public function testCurrentAddsBreadcrumbWithoutHref(): void
    {
        $builder = new BreadcrumbListBuilder();
        $builder->current('Page');

        $items = $builder->all();

        self::assertCount(1, $items);

        $payload = $items[0]->jsonSerialize();

        self::assertSame('Page', $payload['label']);
        self::assertArrayNotHasKey('href', $payload);
    }

    #[Test]
    public function testFluentChain(): void
    {
        $builder = new BreadcrumbListBuilder();
        $builder->item('A', '/a')->item('B', '/b')->current('C');

        $items = $builder->all();

        self::assertCount(3, $items);
        self::assertSame('A', $items[0]->jsonSerialize()['label']);
        self::assertSame('B', $items[1]->jsonSerialize()['label']);
        self::assertSame('C', $items[2]->jsonSerialize()['label']);
        self::assertArrayNotHasKey('href', $items[2]->jsonSerialize());
    }

    #[Test]
    public function testReturnsBreadcrumbInterfaces(): void
    {
        $builder = new BreadcrumbListBuilder();
        $builder->item('Home', '/')->current('Here');

        $items = $builder->all();

        foreach ($items as $item) {
            self::assertInstanceOf(BreadcrumbInterface::class, $item);
        }
    }
}

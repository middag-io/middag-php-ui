<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.com.br>
 * @copyright   2026 MIDDAG (https://www.middag.com.br)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Tests\Builder;

use Middag\Ui\Builder\RegionBuilder;
use Middag\Ui\Contract\BlockDescriptorInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(RegionBuilder::class)]
final class RegionBuilderTest extends TestCase
{
    #[Test]
    public function testEmptyRegion(): void
    {
        $builder = new RegionBuilder();

        self::assertSame([], $builder->all());
    }

    #[Test]
    public function testMetricCard(): void
    {
        $builder = new RegionBuilder();
        $builder->metricCard('m', 'Revenue');

        $blocks = $builder->all();

        self::assertCount(1, $blocks);
        self::assertSame('metric_card', $blocks[0]->jsonSerialize()['type']);
    }

    #[Test]
    public function testDenseTable(): void
    {
        $builder = new RegionBuilder();
        $builder->denseTable('t', 'Users');

        $blocks = $builder->all();

        self::assertCount(1, $blocks);
        self::assertSame('dense_table', $blocks[0]->jsonSerialize()['type']);
    }

    #[Test]
    public function testFluentChain(): void
    {
        $builder = new RegionBuilder();
        $builder
            ->metricCard('m1')
            ->metricCard('m2')
            ->denseTable('t1');

        self::assertCount(3, $builder->all());
    }

    #[Test]
    public function testGenericBlock(): void
    {
        $builder = new RegionBuilder();
        $builder->block('custom_type', 'k', ['foo' => 'bar']);

        $blocks = $builder->all();

        self::assertCount(1, $blocks);

        $payload = $blocks[0]->jsonSerialize();

        self::assertSame('custom_type', $payload['type']);
        self::assertSame('k', $payload['key']);
        self::assertSame(['foo' => 'bar'], $payload['data']);
    }

    #[Test]
    public function testAllReturnsBlockDescriptorInterfaces(): void
    {
        $builder = new RegionBuilder();
        $builder
            ->metricCard('m')
            ->denseTable('t')
            ->block('custom', 'c');

        foreach ($builder->all() as $block) {
            self::assertInstanceOf(BlockDescriptorInterface::class, $block);
        }
    }
}

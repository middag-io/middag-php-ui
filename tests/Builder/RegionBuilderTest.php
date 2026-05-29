<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Tests\Builder;

use Middag\Ui\Builder\RegionBuilder;
use Middag\Ui\Contract\BlockDescriptorInterface;
use Middag\Ui\Enum\ChartType;
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
    public function testChartReturnsSelfAndAppendsBlock(): void
    {
        $builder = new RegionBuilder();

        self::assertSame($builder, $builder->chart('c', ChartType::AREA, [['name' => 'A', 'data' => [1.0]]]));

        $blocks = $builder->all();
        self::assertCount(1, $blocks);
        self::assertSame('chart', $blocks[0]->jsonSerialize()['type']);
    }

    #[Test]
    public function testTabsReturnsSelfAndAppendsBlock(): void
    {
        $builder = new RegionBuilder();

        self::assertSame($builder, $builder->tabs('tb', [['id' => 'a', 'label' => 'A', 'blocks' => []]]));

        $blocks = $builder->all();
        self::assertCount(1, $blocks);
        self::assertSame('tabs', $blocks[0]->jsonSerialize()['type']);
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

    #[Test]
    public function testStatusStrip(): void
    {
        $builder = new RegionBuilder();
        $builder->statusStrip('s', ['items' => []]);

        self::assertSame('status_strip', $builder->all()[0]->jsonSerialize()['type']);
    }

    #[Test]
    public function testDetailPanel(): void
    {
        $builder = new RegionBuilder();
        $builder->detailPanel('d', ['fields' => []]);

        self::assertSame('detail_panel', $builder->all()[0]->jsonSerialize()['type']);
    }

    #[Test]
    public function testActivityTimeline(): void
    {
        $builder = new RegionBuilder();
        $builder->activityTimeline('a', 'History');

        self::assertSame('activity_timeline', $builder->all()[0]->jsonSerialize()['type']);
    }

    #[Test]
    public function testEmptyState(): void
    {
        $builder = new RegionBuilder();
        $builder->emptyState('e', ['title' => 'Nothing here']);

        self::assertSame('empty_state', $builder->all()[0]->jsonSerialize()['type']);
    }

    #[Test]
    public function testFormPanel(): void
    {
        $builder = new RegionBuilder();
        $builder->formPanel('f', 'Edit');

        self::assertSame('form_panel', $builder->all()[0]->jsonSerialize()['type']);
    }

    #[Test]
    public function testMarkdownPanel(): void
    {
        $builder = new RegionBuilder();
        $builder->markdownPanel('md', '# Title');

        self::assertSame('markdown_panel', $builder->all()[0]->jsonSerialize()['type']);
    }
}

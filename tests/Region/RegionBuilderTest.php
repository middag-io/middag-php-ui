<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Tests\Region;

use Middag\Ui\Block\BlockBuilder;
use Middag\Ui\Block\BlockDescriptorInterface;
use Middag\Ui\Block\ChartSeries;
use Middag\Ui\Page\Tab;
use Middag\Ui\Region\RegionBuilder;
use Middag\Ui\Shared\Enum\ChartType;
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

        self::assertSame($builder, $builder->chart('c', ChartType::AREA, [new ChartSeries('A', [1.0])]));

        $blocks = $builder->all();
        self::assertCount(1, $blocks);
        self::assertSame('chart', $blocks[0]->jsonSerialize()['type']);
    }

    #[Test]
    public function testTabsReturnsSelfAndAppendsBlock(): void
    {
        $builder = new RegionBuilder();

        self::assertSame($builder, $builder->tabs('tb', [new Tab('a', 'A')]));

        $blocks = $builder->all();
        self::assertCount(1, $blocks);
        self::assertSame('tabs', $blocks[0]->jsonSerialize()['type']);
    }

    #[Test]
    public function testMetricCard(): void
    {
        $builder = new RegionBuilder();
        $builder->metricCard('m', 1200, 'Revenue');

        $blocks = $builder->all();

        self::assertCount(1, $blocks);

        $payload = $blocks[0]->jsonSerialize();
        self::assertSame('metric_card', $payload['type']);
        // Same shape as BlockBuilder::metricCard (value/label), no forced title.
        self::assertSame(1200, $payload['data']['value']);
        self::assertSame('Revenue', $payload['data']['label']);
    }

    #[Test]
    public function testDenseTable(): void
    {
        $builder = new RegionBuilder();
        $builder->denseTable('t', ['name'], [['name' => 'Ada']]);

        $blocks = $builder->all();

        self::assertCount(1, $blocks);

        $payload = $blocks[0]->jsonSerialize();
        self::assertSame('dense_table', $payload['type']);
        self::assertSame(['name'], $payload['data']['columns']);
        self::assertSame([['name' => 'Ada']], $payload['data']['rows']);
    }

    #[Test]
    public function testFluentChain(): void
    {
        $builder = new RegionBuilder();
        $builder
            ->metricCard('m1', 1, 'A')
            ->metricCard('m2', 2, 'B')
            ->denseTable('t1', []);

        self::assertCount(3, $builder->all());
    }

    #[Test]
    public function testRegionBuilderShapeMatchesBlockBuilder(): void
    {
        // The whole point of #2: same type via RegionBuilder or BlockBuilder
        // must produce an identical descriptor (RegionBuilder delegates).
        $region = (new RegionBuilder())->metricCard('m', 1200, 'Revenue', delta: '+5%');
        $factory = BlockBuilder::metricCard('m', 1200, 'Revenue', '+5%');

        self::assertSame(
            $factory->jsonSerialize(),
            $region->all()[0]->jsonSerialize(),
        );
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
            ->metricCard('m', 1, 'A')
            ->denseTable('t', [])
            ->block('custom', 'c');

        foreach ($builder->all() as $block) {
            self::assertInstanceOf(BlockDescriptorInterface::class, $block);
        }
    }

    #[Test]
    public function testStatusStrip(): void
    {
        $builder = new RegionBuilder();
        $builder->statusStrip('s', [], 'positive');

        self::assertSame('status_strip', $builder->all()[0]->jsonSerialize()['type']);
    }

    #[Test]
    public function testDetailPanel(): void
    {
        $builder = new RegionBuilder();
        $builder->detailPanel('d', []);

        self::assertSame('detail_panel', $builder->all()[0]->jsonSerialize()['type']);
    }

    #[Test]
    public function testActivityTimeline(): void
    {
        $builder = new RegionBuilder();
        $builder->activityTimeline('a', [], hasMore: true, loadMoreHref: '/more');

        self::assertSame('activity_timeline', $builder->all()[0]->jsonSerialize()['type']);
    }

    #[Test]
    public function testEmptyState(): void
    {
        $builder = new RegionBuilder();
        $builder->emptyState('e', 'first-use', 'Nothing here');

        self::assertSame('empty_state', $builder->all()[0]->jsonSerialize()['type']);
    }

    #[Test]
    public function testFormPanel(): void
    {
        $builder = new RegionBuilder();
        $builder->formPanel('f', '/submit', 'PUT', steps: []);

        self::assertSame('form_panel', $builder->all()[0]->jsonSerialize()['type']);
    }

    #[Test]
    public function testMarkdownPanel(): void
    {
        $builder = new RegionBuilder();
        $builder->markdownPanel('md', '# Title', 400);

        self::assertSame('markdown_panel', $builder->all()[0]->jsonSerialize()['type']);
    }

    #[Test]
    public function testCardGrid(): void
    {
        $builder = new RegionBuilder();
        $builder->cardGrid('cg', ['name'], [['name' => 'Ada']], 'compact');

        self::assertSame('card_grid', $builder->all()[0]->jsonSerialize()['type']);
    }

    #[Test]
    public function testActionGrid(): void
    {
        $builder = new RegionBuilder();
        $builder->actionGrid('ag', [], ['success' => true, 'message' => 'Done']);

        self::assertSame('action_grid', $builder->all()[0]->jsonSerialize()['type']);
    }

    #[Test]
    public function testLinkList(): void
    {
        $builder = new RegionBuilder();
        $builder->linkList('ll', [['label' => 'Home', 'href' => '/']]);

        self::assertSame('link_list', $builder->all()[0]->jsonSerialize()['type']);
    }
}

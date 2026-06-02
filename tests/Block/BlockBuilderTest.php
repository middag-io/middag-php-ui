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

use Middag\Ui\Block\BlockBuilder;
use Middag\Ui\Block\BlockDescriptor;
use Middag\Ui\Block\ChartSeries;
use Middag\Ui\Form\FormStep;
use Middag\Ui\Page\Tab;
use Middag\Ui\Shared\Data\Translatable;
use Middag\Ui\Shared\Enum\ChartType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(BlockBuilder::class)]
final class BlockBuilderTest extends TestCase
{
    #[Test]
    public function testDenseTable(): void
    {
        $block = BlockBuilder::denseTable('t', [['key' => 'name']], [['name' => 'John']]);

        self::assertInstanceOf(BlockDescriptor::class, $block);

        $payload = $block->jsonSerialize();

        self::assertSame('dense_table', $payload['type']);
        self::assertSame('t', $payload['key']);
        self::assertSame([['key' => 'name']], $payload['data']['columns']);
        self::assertSame([['name' => 'John']], $payload['data']['rows']);
    }

    #[Test]
    public function testChartMinimal(): void
    {
        $payload = BlockBuilder::chart('c', ChartType::LINE, [new ChartSeries('A', [1.0, 2.0])])->jsonSerialize();

        self::assertSame('chart', $payload['type']);
        self::assertSame('line', $payload['data']['chartType']);
        // Series VOs are serialized to a pure array tree.
        self::assertSame([['name' => 'A', 'data' => [1.0, 2.0]]], $payload['data']['series']);
        self::assertArrayNotHasKey('categories', $payload['data']);
        self::assertArrayNotHasKey('options', $payload['data']);
    }

    #[Test]
    public function testChartWithCategoriesAndOptions(): void
    {
        $payload = BlockBuilder::chart(
            'c',
            ChartType::BAR,
            [new ChartSeries('A', [1.0])],
            ['Jan', 'Feb'],
            ['stacked' => true],
        )->jsonSerialize();

        self::assertSame('bar', $payload['data']['chartType']);
        self::assertSame(['Jan', 'Feb'], $payload['data']['categories']);
        self::assertSame(['stacked' => true], $payload['data']['options']);
    }

    #[Test]
    public function testTabs(): void
    {
        $payload = BlockBuilder::tabs('tb', [new Tab('a', 'A')])->jsonSerialize();

        self::assertSame('tabs', $payload['type']);
        self::assertSame([['id' => 'a', 'label' => 'A', 'blocks' => []]], $payload['data']['tabs']);
    }

    #[Test]
    public function testTabsSerializeNestedBlocksAndTranslatableLabelPurely(): void
    {
        $tab = new Tab('a', Translatable::of('tab_a', 'forms'), [BlockBuilder::markdownPanel('m', '# Hi')]);
        $payload = BlockBuilder::tabs('tb', [$tab])->jsonSerialize();

        $serialized = $payload['data']['tabs'][0];

        // Pure array tree: Translatable label and nested block become arrays.
        self::assertSame(['key' => 'tab_a', 'domain' => 'forms'], $serialized['label']);
        self::assertIsArray($serialized['blocks'][0]);
        self::assertSame('markdown_panel', $serialized['blocks'][0]['type']);
    }

    #[Test]
    public function testFormPanelWithSteps(): void
    {
        $steps = [new FormStep(id: 's1', label: 'Step 1', fields: ['name'])];
        $payload = BlockBuilder::formPanel('f', '/submit', 'POST', [], [], $steps)->jsonSerialize();

        self::assertTrue($payload['data']['multiStep']);
        self::assertSame($steps, $payload['data']['steps']);
    }

    #[Test]
    public function testFormPanel(): void
    {
        $block = BlockBuilder::formPanel('f', '/submit', 'POST', [], []);

        self::assertInstanceOf(BlockDescriptor::class, $block);

        $payload = $block->jsonSerialize();

        self::assertSame('form_panel', $payload['type']);
        self::assertSame('f', $payload['key']);
        self::assertSame('/submit', $payload['data']['action']);
        // formPanel() lowercases the method for the wire: @middag-io/react's
        // FormPanelBlock matches `data.method === "post"/"put"` (lowercase).
        self::assertSame('post', $payload['data']['method']);
        self::assertArrayHasKey('schema', $payload['data']);
        self::assertArrayHasKey('values', $payload['data']);
    }

    #[Test]
    public function testDetailPanel(): void
    {
        $block = BlockBuilder::detailPanel('d', [['title' => 'Info']]);

        self::assertInstanceOf(BlockDescriptor::class, $block);

        $payload = $block->jsonSerialize();

        self::assertSame('detail_panel', $payload['type']);
        self::assertSame('d', $payload['key']);
        self::assertSame([['title' => 'Info']], $payload['data']['sections']);
    }

    #[Test]
    public function testMetricCard(): void
    {
        $block = BlockBuilder::metricCard('m', 42, 'Users', '+5%');

        self::assertInstanceOf(BlockDescriptor::class, $block);

        $payload = $block->jsonSerialize();

        self::assertSame('metric_card', $payload['type']);
        self::assertSame('m', $payload['key']);
        self::assertSame(42, $payload['data']['value']);
        self::assertSame('Users', $payload['data']['label']);
        self::assertSame('+5%', $payload['data']['delta']);
        self::assertArrayNotHasKey('icon', $payload['data']);
        self::assertArrayNotHasKey('href', $payload['data']);
    }

    #[Test]
    public function testEmptyState(): void
    {
        $block = BlockBuilder::emptyState('e');

        self::assertInstanceOf(BlockDescriptor::class, $block);

        $payload = $block->jsonSerialize();

        self::assertSame('empty_state', $payload['type']);
        self::assertSame('e', $payload['key']);
        self::assertSame('first-use', $payload['data']['variant']);
    }

    #[Test]
    public function testStatusStrip(): void
    {
        $block = BlockBuilder::statusStrip('s', [['label' => 'Active']]);

        self::assertInstanceOf(BlockDescriptor::class, $block);

        $payload = $block->jsonSerialize();

        self::assertSame('status_strip', $payload['type']);
        self::assertSame('s', $payload['key']);
        self::assertSame([['label' => 'Active']], $payload['data']['items']);
    }

    #[Test]
    public function testActivityTimeline(): void
    {
        $block = BlockBuilder::activityTimeline('a', [], hasMore: true, loadMoreHref: '/more');

        self::assertInstanceOf(BlockDescriptor::class, $block);

        $payload = $block->jsonSerialize();

        self::assertSame('activity_timeline', $payload['type']);
        self::assertSame('a', $payload['key']);
        self::assertArrayHasKey('groups', $payload['data']);
        // Wire keys are camelCase, consistent with the rest of the contract.
        self::assertTrue($payload['data']['hasMore']);
        self::assertSame('/more', $payload['data']['loadMoreHref']);
    }

    #[Test]
    public function testMarkdownPanel(): void
    {
        $block = BlockBuilder::markdownPanel('md', '# Hello', 400);

        self::assertInstanceOf(BlockDescriptor::class, $block);

        $payload = $block->jsonSerialize();

        self::assertSame('markdown_panel', $payload['type']);
        self::assertSame('md', $payload['key']);
        self::assertSame('# Hello', $payload['data']['content']);
        self::assertSame(400, $payload['data']['maxHeight']);
    }

    #[Test]
    public function testCardGrid(): void
    {
        $block = BlockBuilder::cardGrid('cg', [], []);

        self::assertInstanceOf(BlockDescriptor::class, $block);

        $payload = $block->jsonSerialize();

        self::assertSame('card_grid', $payload['type']);
        self::assertSame('cg', $payload['key']);
        self::assertArrayHasKey('columns', $payload['data']);
        self::assertArrayHasKey('rows', $payload['data']);
    }

    #[Test]
    public function testActionGrid(): void
    {
        $block = BlockBuilder::actionGrid('ag', []);

        self::assertInstanceOf(BlockDescriptor::class, $block);

        $payload = $block->jsonSerialize();

        self::assertSame('action_grid', $payload['type']);
        self::assertSame('ag', $payload['key']);
        self::assertArrayHasKey('items', $payload['data']);
    }

    #[Test]
    public function testLinkList(): void
    {
        $block = BlockBuilder::linkList('ll', [['label' => 'Link', 'href' => '/']]);

        self::assertInstanceOf(BlockDescriptor::class, $block);

        $payload = $block->jsonSerialize();

        self::assertSame('link_list', $payload['type']);
        self::assertSame('ll', $payload['key']);
        self::assertSame([['label' => 'Link', 'href' => '/']], $payload['data']['items']);
    }
}

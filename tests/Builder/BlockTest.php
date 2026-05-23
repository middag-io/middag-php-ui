<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.com.br>
 * @copyright   2026 MIDDAG (https://www.middag.com.br)
 * @license     proprietary
 */

namespace Middag\Ui\Tests\Builder;

use Middag\Ui\Builder\Block;
use Middag\Ui\Data\BlockDescriptor;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(Block::class)]
final class BlockTest extends TestCase
{
    #[Test]
    public function testDenseTable(): void
    {
        $block = Block::denseTable('t', [['key' => 'name']], [['name' => 'John']]);

        self::assertInstanceOf(BlockDescriptor::class, $block);

        $payload = $block->jsonSerialize();

        self::assertSame('dense_table', $payload['type']);
        self::assertSame('t', $payload['key']);
        self::assertSame([['key' => 'name']], $payload['data']['columns']);
        self::assertSame([['name' => 'John']], $payload['data']['rows']);
    }

    #[Test]
    public function testFormPanel(): void
    {
        $block = Block::formPanel('f', '/submit', 'POST', [], []);

        self::assertInstanceOf(BlockDescriptor::class, $block);

        $payload = $block->jsonSerialize();

        self::assertSame('form_panel', $payload['type']);
        self::assertSame('f', $payload['key']);
        self::assertSame('/submit', $payload['data']['action']);
        self::assertSame('POST', $payload['data']['method']);
        self::assertArrayHasKey('schema', $payload['data']);
        self::assertArrayHasKey('values', $payload['data']);
    }

    #[Test]
    public function testDetailPanel(): void
    {
        $block = Block::detailPanel('d', [['title' => 'Info']]);

        self::assertInstanceOf(BlockDescriptor::class, $block);

        $payload = $block->jsonSerialize();

        self::assertSame('detail_panel', $payload['type']);
        self::assertSame('d', $payload['key']);
        self::assertSame([['title' => 'Info']], $payload['data']['sections']);
    }

    #[Test]
    public function testMetricCard(): void
    {
        $block = Block::metricCard('m', 42, 'Users', '+5%');

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
        $block = Block::emptyState('e');

        self::assertInstanceOf(BlockDescriptor::class, $block);

        $payload = $block->jsonSerialize();

        self::assertSame('empty_state', $payload['type']);
        self::assertSame('e', $payload['key']);
        self::assertSame('first-use', $payload['data']['variant']);
    }

    #[Test]
    public function testStatusStrip(): void
    {
        $block = Block::statusStrip('s', [['label' => 'Active']]);

        self::assertInstanceOf(BlockDescriptor::class, $block);

        $payload = $block->jsonSerialize();

        self::assertSame('status_strip', $payload['type']);
        self::assertSame('s', $payload['key']);
        self::assertSame([['label' => 'Active']], $payload['data']['items']);
    }

    #[Test]
    public function testActivityTimeline(): void
    {
        $block = Block::activityTimeline('a', []);

        self::assertInstanceOf(BlockDescriptor::class, $block);

        $payload = $block->jsonSerialize();

        self::assertSame('activity_timeline', $payload['type']);
        self::assertSame('a', $payload['key']);
        self::assertArrayHasKey('groups', $payload['data']);
    }

    #[Test]
    public function testMarkdownPanel(): void
    {
        $block = Block::markdownPanel('md', '# Hello');

        self::assertInstanceOf(BlockDescriptor::class, $block);

        $payload = $block->jsonSerialize();

        self::assertSame('markdown_panel', $payload['type']);
        self::assertSame('md', $payload['key']);
        self::assertSame('# Hello', $payload['data']['content']);
    }

    #[Test]
    public function testCardGrid(): void
    {
        $block = Block::cardGrid('cg', [], []);

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
        $block = Block::actionGrid('ag', []);

        self::assertInstanceOf(BlockDescriptor::class, $block);

        $payload = $block->jsonSerialize();

        self::assertSame('action_grid', $payload['type']);
        self::assertSame('ag', $payload['key']);
        self::assertArrayHasKey('items', $payload['data']);
    }

    #[Test]
    public function testLinkList(): void
    {
        $block = Block::linkList('ll', [['label' => 'Link', 'href' => '/']]);

        self::assertInstanceOf(BlockDescriptor::class, $block);

        $payload = $block->jsonSerialize();

        self::assertSame('link_list', $payload['type']);
        self::assertSame('ll', $payload['key']);
        self::assertSame([['label' => 'Link', 'href' => '/']], $payload['data']['items']);
    }
}

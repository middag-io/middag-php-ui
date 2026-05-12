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
use Middag\Ui\Builder\CrudBuilder;
use Middag\Ui\Builder\PageBuilder;
use Middag\Ui\Data\PageAction;
use Middag\Ui\Data\PageContractData;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(PageBuilder::class)]
final class PageBuilderTest extends TestCase
{
    #[Test]
    public function testPageFactory(): void
    {
        $contract = PageBuilder::page('test')->build();

        self::assertInstanceOf(PageContractData::class, $contract);
        self::assertSame('test', $contract->page->key);
    }

    #[Test]
    public function testCrudFactory(): void
    {
        $crud = PageBuilder::crud('App\Entity\User');

        self::assertInstanceOf(CrudBuilder::class, $crud);
    }

    #[Test]
    public function testActionFactory(): void
    {
        $action = PageBuilder::action('a', 'Label', 'primary', '/href');

        self::assertInstanceOf(PageAction::class, $action);
        self::assertSame('a', $action->id);
        self::assertSame('Label', $action->label);
        self::assertSame('primary', $action->intent);
        self::assertSame('/href', $action->href);
    }

    #[Test]
    public function testTitleAndSubtitle(): void
    {
        $contract = PageBuilder::page('test')
            ->title('T')
            ->subtitle('S')
            ->build();

        self::assertSame('T', $contract->page->title);
        self::assertSame('S', $contract->page->subtitle);
    }

    #[Test]
    public function testShellDefault(): void
    {
        $contract = PageBuilder::page('test')->build();

        self::assertSame('product', $contract->shell);
    }

    #[Test]
    public function testShellOverride(): void
    {
        $contract = PageBuilder::page('test')
            ->shell('immersive')
            ->build();

        self::assertSame('immersive', $contract->shell);
    }

    #[Test]
    public function testLayout(): void
    {
        $contract = PageBuilder::page('test')
            ->layout('dashboard')
            ->build();

        self::assertSame('dashboard', $contract->layout->template);
    }

    #[Test]
    public function testRegionWithArray(): void
    {
        $contract = PageBuilder::page('test')
            ->region('content', [Block::empty_state('e')])
            ->build();

        $blocks = $contract->layout->regions['content'] ?? [];

        self::assertCount(1, $blocks);
        self::assertSame('empty_state', $blocks[0]->jsonSerialize()['type']);
    }

    #[Test]
    public function testRegionWithClosure(): void
    {
        $contract = PageBuilder::page('test')
            ->region('content', fn ($r) => $r->metric_card('m'))
            ->build();

        $blocks = $contract->layout->regions['content'] ?? [];

        self::assertCount(1, $blocks);
        self::assertSame('metric_card', $blocks[0]->jsonSerialize()['type']);
    }

    #[Test]
    public function testBreadcrumbs(): void
    {
        $contract = PageBuilder::page('test')
            ->breadcrumbs(fn ($bc) => $bc->item('Home', '/')->current('Here'))
            ->build();

        self::assertCount(2, $contract->page->breadcrumbs);
    }

    #[Test]
    public function testActions(): void
    {
        $action = PageBuilder::action('a', 'Label', 'primary', '/href');

        $contract = PageBuilder::page('test')
            ->actions([$action])
            ->build();

        self::assertCount(1, $contract->page->actions);
        self::assertSame('a', $contract->page->actions[0]->id);
    }

    #[Test]
    public function testBuildReturnsPageContractData(): void
    {
        $contract = PageBuilder::page('test')->build();

        self::assertInstanceOf(PageContractData::class, $contract);
    }

    #[Test]
    public function testToPropsMinimal(): void
    {
        $props = PageBuilder::page('test')->to_props();

        self::assertArrayHasKey('contract', $props);
        self::assertInstanceOf(PageContractData::class, $props['contract']);
        self::assertArrayNotHasKey('overlay', $props);
        self::assertArrayNotHasKey('help', $props);
        self::assertArrayNotHasKey('inspector', $props);
    }

    #[Test]
    public function testToPropsWithOverlay(): void
    {
        $props = PageBuilder::page('test')
            ->overlay()
            ->to_props();

        self::assertArrayHasKey('overlay', $props);
        self::assertTrue($props['overlay']);
    }

    #[Test]
    public function testToPropsWithHelp(): void
    {
        $props = PageBuilder::page('test')
            ->help('T', 'B', 'url')
            ->to_props();

        self::assertArrayHasKey('help', $props);
        self::assertSame('T', $props['help']['title']);
        self::assertSame('B', $props['help']['body']);
        self::assertSame('url', $props['help']['learnMore']);
    }

    #[Test]
    public function testToPropsWithInspector(): void
    {
        $props = PageBuilder::page('test')
            ->inspector('/api/{id}', 500)
            ->to_props();

        self::assertArrayHasKey('inspector', $props);
        self::assertSame('/api/{id}', $props['inspector']->endpoint);
        self::assertSame(500, $props['inspector']->width);
    }
}

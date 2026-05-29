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

use Middag\Ui\Builder\BlockBuilder;
use Middag\Ui\Builder\CrudBuilder;
use Middag\Ui\Builder\PageBuilder;
use Middag\Ui\Data\PageAction;
use Middag\Ui\PageContract;
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

        self::assertInstanceOf(PageContract::class, $contract);
        self::assertSame('test', $contract->page->key);
    }

    #[Test]
    public function testNotifyShortcutsAttachNotifications(): void
    {
        $contract = PageBuilder::page('test')
            ->notifySuccess('Saved')
            ->notifyInfo('Heads up')
            ->notifyWarning('Careful')
            ->notifyError('Failed', 'Oops')
            ->build();

        $payload = $contract->jsonSerialize();

        self::assertCount(4, $payload['notifications']);
        self::assertSame('success', $payload['notifications'][0]['level']);
        self::assertSame('info', $payload['notifications'][1]['level']);
        self::assertSame('warning', $payload['notifications'][2]['level']);
        self::assertSame('error', $payload['notifications'][3]['level']);
        self::assertSame('Oops', $payload['notifications'][3]['title']);
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
            ->region('content', [BlockBuilder::emptyState('e')])
            ->build();

        $blocks = $contract->layout->regions['content'] ?? [];

        self::assertCount(1, $blocks);
        self::assertSame('empty_state', $blocks[0]->jsonSerialize()['type']);
    }

    #[Test]
    public function testRegionWithClosure(): void
    {
        $contract = PageBuilder::page('test')
            ->region('content', fn ($r) => $r->metricCard('m'))
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
    public function testBuildReturnsPageContract(): void
    {
        $contract = PageBuilder::page('test')->build();

        self::assertInstanceOf(PageContract::class, $contract);
    }

    #[Test]
    public function testToPropsMinimal(): void
    {
        $props = PageBuilder::page('test')->toProps();

        self::assertArrayHasKey('contract', $props);
        self::assertInstanceOf(PageContract::class, $props['contract']);
        self::assertArrayNotHasKey('overlay', $props);
        self::assertArrayNotHasKey('help', $props);
        self::assertArrayNotHasKey('inspector', $props);
    }

    #[Test]
    public function testToPropsWithOverlay(): void
    {
        $props = PageBuilder::page('test')
            ->overlay()
            ->toProps();

        self::assertArrayHasKey('overlay', $props);
        self::assertTrue($props['overlay']);
    }

    #[Test]
    public function testToPropsWithHelp(): void
    {
        $props = PageBuilder::page('test')
            ->help('T', 'B', 'url')
            ->toProps();

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
            ->toProps();

        self::assertArrayHasKey('inspector', $props);
        self::assertSame('/api/{id}', $props['inspector']->endpoint);
        self::assertSame(500, $props['inspector']->width);
    }

    #[Test]
    public function testMetaIsFluentAndBuildable(): void
    {
        $page = PageBuilder::page('wizard');

        $result = $page->meta(['multiStep' => true, 'steps' => 3]);

        self::assertSame($page, $result);
        self::assertInstanceOf(PageContract::class, $page->build());
    }
}

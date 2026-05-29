<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Tests;

use Middag\Ui\Data\BlockDescriptor;
use Middag\Ui\Data\LayoutDescriptor;
use Middag\Ui\Data\PageMeta;
use Middag\Ui\Data\PageResources;
use Middag\Ui\PageContract;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(PageContract::class)]
final class PageContractTest extends TestCase
{
    #[Test]
    public function testSerializesCompleteContract(): void
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

        $page = new PageMeta(key: 'users.index', title: 'Users');

        $contract = new PageContract(
            shell: 'product',
            page: $page,
            layout: $layout,
        );

        $payload = $contract->jsonSerialize();

        self::assertSame('1', $payload['version']);
        self::assertSame('product', $payload['shell']);
        self::assertSame('users.index', $payload['page']['key']);
        self::assertSame('Users', $payload['page']['title']);
        self::assertSame('stack', $payload['layout']['template']);
        self::assertArrayHasKey('main', $payload['layout']['regions']);
    }

    #[Test]
    public function testOmitsResourcesWhenNull(): void
    {
        $layout = new LayoutDescriptor(template: 'stack', regions: ['main' => []]);
        $page = new PageMeta(key: 'home', title: 'Home');

        $contract = new PageContract(
            shell: 'product',
            page: $page,
            layout: $layout,
        );

        $payload = $contract->jsonSerialize();

        self::assertArrayNotHasKey('resources', $payload);
    }

    #[Test]
    public function testIncludesResourcesWhenSet(): void
    {
        $layout = new LayoutDescriptor(template: 'stack', regions: ['main' => []]);
        $page = new PageMeta(key: 'home', title: 'Home');
        $resources = new PageResources();

        $contract = new PageContract(
            shell: 'product',
            page: $page,
            layout: $layout,
            resources: $resources,
        );

        $payload = $contract->jsonSerialize();

        self::assertArrayHasKey('resources', $payload);
        self::assertIsArray($payload['resources']);
    }

    #[Test]
    public function testVersionConstant(): void
    {
        self::assertSame('1', PageContract::VERSION);
    }
}

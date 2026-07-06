<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Tests\Page;

use Middag\Ui\Block\BlockDescriptor;
use Middag\Ui\Block\LayoutDescriptor;
use Middag\Ui\Envelope\Contract\ContractEnvelopeInterface;
use Middag\Ui\Page\PageContract;
use Middag\Ui\Page\PageMeta;
use Middag\Ui\Page\PageResources;
use Middag\Ui\Shared\Enum\NotificationLevel;
use Middag\Ui\Shared\ValueObject\Notification;
use Middag\Ui\Tests\Support\ValidatesAgainstSchema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(PageContract::class)]
final class PageContractTest extends TestCase
{
    use ValidatesAgainstSchema;

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
    public function testOmitsNotificationsWhenEmpty(): void
    {
        $contract = new PageContract(
            shell: 'product',
            page: new PageMeta(key: 'home', title: 'Home'),
            layout: new LayoutDescriptor(template: 'stack', regions: ['main' => []]),
        );

        self::assertArrayNotHasKey('notifications', $contract->jsonSerialize());
    }

    #[Test]
    public function testIncludesNotificationsWhenSet(): void
    {
        $contract = new PageContract(
            shell: 'product',
            page: new PageMeta(key: 'home', title: 'Home'),
            layout: new LayoutDescriptor(template: 'stack', regions: ['main' => []]),
            notifications: [new Notification(NotificationLevel::SUCCESS, 'Saved')],
        );

        $payload = $contract->jsonSerialize();

        self::assertCount(1, $payload['notifications']);
        self::assertSame('success', $payload['notifications'][0]['level']);
    }

    #[Test]
    public function testVersionConstant(): void
    {
        self::assertSame('1', PageContract::VERSION);
    }

    #[Test]
    public function testSharesEnvelopeVersionWithInterface(): void
    {
        self::assertSame(ContractEnvelopeInterface::VERSION, PageContract::VERSION);

        $contract = new PageContract(
            shell: 'product',
            page: new PageMeta(key: 'home', title: 'Home'),
            layout: new LayoutDescriptor(template: 'stack', regions: ['main' => []]),
        );

        self::assertInstanceOf(ContractEnvelopeInterface::class, $contract);
    }

    #[Test]
    public function testSchemaAcceptsAMinimalContract(): void
    {
        $contract = new PageContract(
            shell: 'product',
            page: new PageMeta(key: 'users.index', title: 'Users'),
            layout: new LayoutDescriptor(
                template: 'stack',
                regions: ['main' => [new BlockDescriptor(type: 'dense_table', key: 'users', data: ['columns' => []])]],
            ),
        );

        $this->assertValidAgainst('PageContract', $contract);
    }

    #[Test]
    public function testSchemaAcceptsAContractWithResourcesAndNotifications(): void
    {
        $contract = new PageContract(
            shell: 'product',
            page: new PageMeta(key: 'home', title: 'Home'),
            layout: new LayoutDescriptor(template: 'stack', regions: ['main' => []]),
            resources: new PageResources(),
            notifications: [new Notification(NotificationLevel::SUCCESS, 'Saved')],
            entities: ['user' => '/users/:id'],
        );

        $this->assertValidAgainst('PageContract', $contract);
    }

    #[Test]
    public function testSchemaRejectsAContractMissingItsLayout(): void
    {
        $payload = $this->minimalContract()->jsonSerialize();
        unset($payload['layout']);

        $this->assertInvalidAgainst('PageContract', $payload);
    }

    #[Test]
    public function testSchemaRejectsAWrongVersionConstant(): void
    {
        $payload = $this->minimalContract()->jsonSerialize();
        $payload['version'] = '2';

        $this->assertInvalidAgainst('PageContract', $payload);
    }

    #[Test]
    public function testSchemaRejectsAnUnknownTopLevelProperty(): void
    {
        $payload = $this->minimalContract()->jsonSerialize();
        $payload['extra'] = true;

        $this->assertInvalidAgainst('PageContract', $payload);
    }

    private function minimalContract(): PageContract
    {
        return new PageContract(
            shell: 'product',
            page: new PageMeta(key: 'home', title: 'Home'),
            layout: new LayoutDescriptor(template: 'stack', regions: ['main' => []]),
        );
    }
}

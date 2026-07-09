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

use Middag\Ui\Block\BlockDescriptor;
use Middag\Ui\Envelope\Contract\ContractEnvelopeInterface;
use Middag\Ui\Page\ResourcePatch;
use Middag\Ui\Region\Fragment;
use Middag\Ui\Region\RegionUpdate;
use Middag\Ui\Shared\Enum\FragmentKind;
use Middag\Ui\Shared\Enum\NotificationLevel;
use Middag\Ui\Shared\ValueObject\Notification;
use Middag\Ui\Table\TableConfig;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @internal
 */
#[CoversClass(Fragment::class)]
final class FragmentTest extends TestCase
{
    #[Test]
    public function testIsReadonlyClass(): void
    {
        self::assertTrue((new ReflectionClass(Fragment::class))->isReadOnly());
    }

    #[Test]
    public function testBlockIsSelfDescribingEnvelope(): void
    {
        $fragment = Fragment::block(new BlockDescriptor(type: 'metric_card', key: 'rev', data: []));

        self::assertInstanceOf(ContractEnvelopeInterface::class, $fragment);
        self::assertSame(FragmentKind::Block, $fragment->kind);

        $payload = $fragment->jsonSerialize();

        self::assertSame('1', $payload['version']);
        self::assertSame('block', $payload['kind']);
        self::assertSame('metric_card', $payload['payload']['type']);
        self::assertArrayNotHasKey('notifications', $payload);
        self::assertArrayNotHasKey('resources', $payload);
        self::assertArrayNotHasKey('customType', $payload);
    }

    #[Test]
    public function testRegionWrapsRegionUpdate(): void
    {
        $payload = Fragment::region(RegionUpdate::replace('content'))->jsonSerialize();

        self::assertSame('region', $payload['kind']);
        self::assertSame('content', $payload['payload']['region']);
    }

    #[Test]
    public function testTableWrapsTableConfig(): void
    {
        $payload = Fragment::table(new TableConfig(columns: []))->jsonSerialize();

        self::assertSame('table', $payload['kind']);
        self::assertArrayHasKey('payload', $payload);
    }

    #[Test]
    public function testFormWrapsBlock(): void
    {
        $payload = Fragment::form(new BlockDescriptor(type: 'form_panel', key: 'edit', data: []))->jsonSerialize();

        self::assertSame('form', $payload['kind']);
        self::assertSame('form_panel', $payload['payload']['type']);
    }

    #[Test]
    public function testNotificationsCarriesNoPayload(): void
    {
        $payload = Fragment::notifications([
            new Notification(NotificationLevel::Success, 'Saved'),
        ])->jsonSerialize();

        self::assertSame('notifications', $payload['kind']);
        self::assertArrayNotHasKey('payload', $payload);
        self::assertCount(1, $payload['notifications']);
        self::assertSame('success', $payload['notifications'][0]['level']);
    }

    #[Test]
    public function testCustomEmitsCustomType(): void
    {
        $payload = Fragment::custom('host:widget', new BlockDescriptor(type: 'x', key: 'k', data: []))
            ->jsonSerialize();

        self::assertSame('custom', $payload['kind']);
        self::assertSame('host:widget', $payload['customType']);
    }

    #[Test]
    public function testOfRoutesArbitraryKind(): void
    {
        $payload = Fragment::of(FragmentKind::Detail, new BlockDescriptor(type: 'detail_panel', key: 'd', data: []))
            ->jsonSerialize();

        self::assertSame('detail', $payload['kind']);
        self::assertSame('detail_panel', $payload['payload']['type']);
        self::assertArrayNotHasKey('customType', $payload);
    }

    #[Test]
    public function testOfCustomWithoutCustomTypeOmitsIt(): void
    {
        $payload = Fragment::of(FragmentKind::Custom, new BlockDescriptor(type: 'x', key: 'k', data: []))
            ->jsonSerialize();

        self::assertSame('custom', $payload['kind']);
        self::assertArrayNotHasKey('customType', $payload);
    }

    #[Test]
    public function testWithNotificationsAttachesSideChannel(): void
    {
        $payload = Fragment::block(new BlockDescriptor(type: 'metric_card', key: 'rev', data: []))
            ->withNotifications(new Notification(NotificationLevel::Info, 'Updated'))
            ->jsonSerialize();

        self::assertArrayHasKey('payload', $payload);
        self::assertCount(1, $payload['notifications']);
        self::assertSame('info', $payload['notifications'][0]['level']);
    }

    #[Test]
    public function testWithResourcesAttachesPatch(): void
    {
        $payload = Fragment::block(new BlockDescriptor(type: 'metric_card', key: 'rev', data: []))
            ->withResources(new ResourcePatch(capabilities: ['x' => true]))
            ->jsonSerialize();

        self::assertSame(['x' => true], $payload['resources']['capabilities']);
    }
}

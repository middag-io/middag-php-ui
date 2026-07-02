<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Tests\Action;

use Middag\Ui\Action\ActionResult;
use Middag\Ui\Block\BlockDescriptor;
use Middag\Ui\Page\ResourcePatch;
use Middag\Ui\Region\Fragment;
use Middag\Ui\Shared\Enum\NotificationLevel;
use Middag\Ui\Shared\ValueObject\Notification;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @internal
 */
#[CoversClass(ActionResult::class)]
final class ActionResultTest extends TestCase
{
    #[Test]
    public function testIsReadonlyClass(): void
    {
        self::assertTrue((new ReflectionClass(ActionResult::class))->isReadOnly());
    }

    #[Test]
    public function testSerializesMinimal(): void
    {
        $payload = (new ActionResult())->jsonSerialize();

        self::assertSame(['success' => true], $payload);
    }

    #[Test]
    public function testSerializesAllFields(): void
    {
        $payload = (new ActionResult(
            success: false,
            notifications: [new Notification(NotificationLevel::ERROR, 'Failed')],
            redirect: '/back',
            refreshBlocks: ['table'],
            errors: ['name' => 'Required'],
        ))->jsonSerialize();

        self::assertFalse($payload['success']);
        self::assertCount(1, $payload['notifications']);
        self::assertSame('error', $payload['notifications'][0]['level']);
        self::assertSame('/back', $payload['redirect']);
        self::assertSame(['table'], $payload['refreshBlocks']);
        self::assertSame(['name' => 'Required'], $payload['errors']);
    }

    #[Test]
    public function testOmitsPushFieldsByDefault(): void
    {
        $payload = (new ActionResult())->jsonSerialize();

        self::assertArrayNotHasKey('fragments', $payload);
        self::assertArrayNotHasKey('resources', $payload);
    }

    #[Test]
    public function testSerializesPushFields(): void
    {
        $payload = (new ActionResult(
            fragments: [Fragment::block(new BlockDescriptor(type: 'metric_card', key: 'rev', data: []))],
            resources: new ResourcePatch(capabilities: ['user:edit' => true]),
        ))->jsonSerialize();

        self::assertCount(1, $payload['fragments']);
        self::assertSame('block', $payload['fragments'][0]['kind']);
        self::assertSame(['user:edit' => true], $payload['resources']['capabilities']);
    }
}

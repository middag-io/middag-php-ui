<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Tests\Data;

use Middag\Ui\Data\ActionResult;
use Middag\Ui\Data\Notification;
use Middag\Ui\Enum\NotificationLevel;
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
}

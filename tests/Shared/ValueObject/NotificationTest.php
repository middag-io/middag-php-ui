<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Tests\Shared\ValueObject;

use Middag\Ui\Action\Action;
use Middag\Ui\Action\ActionTarget;
use Middag\Ui\Shared\Enum\NotificationLevel;
use Middag\Ui\Shared\ValueObject\Notification;
use Middag\Ui\Shared\ValueObject\Translatable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @internal
 */
#[CoversClass(Notification::class)]
final class NotificationTest extends TestCase
{
    #[Test]
    public function testIsReadonlyClass(): void
    {
        self::assertTrue((new ReflectionClass(Notification::class))->isReadOnly());
    }

    #[Test]
    public function testSerializesMinimal(): void
    {
        $payload = (new Notification(NotificationLevel::SUCCESS, 'Saved'))->jsonSerialize();

        self::assertSame([
            'level' => 'success',
            'message' => 'Saved',
            'dismissible' => true,
        ], $payload);
    }

    #[Test]
    public function testSerializesAllFields(): void
    {
        $payload = (new Notification(
            level: NotificationLevel::ERROR,
            message: Translatable::of('failed', 'local_x'),
            title: 'Oops',
            dismissible: false,
            timeout: 5000,
        ))->jsonSerialize();

        self::assertSame('error', $payload['level']);
        self::assertSame(['key' => 'failed', 'domain' => 'local_x'], $payload['message']);
        self::assertSame('Oops', $payload['title']);
        self::assertFalse($payload['dismissible']);
        self::assertSame(5000, $payload['timeout']);
    }

    #[Test]
    public function testSerializesActionWhenSet(): void
    {
        $payload = (new Notification(
            level: NotificationLevel::INFO,
            message: 'Deleted',
            action: new Action(id: 'undo', label: 'Undo', target: ActionTarget::request('/undo')),
        ))->jsonSerialize();

        self::assertArrayHasKey('action', $payload);
        self::assertSame('undo', $payload['action']['id']);
        self::assertSame('request', $payload['action']['target']['kind']);
    }
}

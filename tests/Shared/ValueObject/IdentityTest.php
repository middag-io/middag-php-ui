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

use Middag\Ui\Shared\ValueObject\Identity;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @internal
 */
#[CoversClass(Identity::class)]
final class IdentityTest extends TestCase
{
    #[Test]
    public function testIsReadonlyClass(): void
    {
        self::assertTrue((new ReflectionClass(Identity::class))->isReadOnly());
    }

    #[Test]
    public function testSerializesMinimal(): void
    {
        $payload = (new Identity(id: '42', name: 'Ada'))->jsonSerialize();

        self::assertSame(['id' => '42', 'name' => 'Ada'], $payload);
        self::assertArrayNotHasKey('email', $payload);
        self::assertArrayNotHasKey('roles', $payload);
    }

    #[Test]
    public function testSerializesAllFields(): void
    {
        $payload = (new Identity(
            id: '42',
            name: 'Ada',
            email: 'ada@example.com',
            avatarUrl: 'https://x/a.png',
            roles: ['admin'],
        ))->jsonSerialize();

        self::assertSame('ada@example.com', $payload['email']);
        self::assertSame('https://x/a.png', $payload['avatarUrl']);
        self::assertSame(['admin'], $payload['roles']);
    }
}

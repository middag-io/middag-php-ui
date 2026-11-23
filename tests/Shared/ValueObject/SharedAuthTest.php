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

use InvalidArgumentException;
use Middag\Ui\Shared\ValueObject\Identity;
use Middag\Ui\Shared\ValueObject\SharedAuth;
use Middag\Ui\Tests\Support\ValidatesAgainstSchema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @internal
 */
#[CoversClass(SharedAuth::class)]
final class SharedAuthTest extends TestCase
{
    use ValidatesAgainstSchema;

    #[Test]
    public function testIsReadonlyClass(): void
    {
        self::assertTrue((new ReflectionClass(SharedAuth::class))->isReadOnly());
    }

    #[Test]
    public function testAlwaysEmitsCapabilitiesAndOmitsAbsentOptionals(): void
    {
        self::assertSame(
            '{"id":"1","name":"Ada","capabilities":[]}',
            json_encode(new SharedAuth(new Identity(id: '1', name: 'Ada'))),
        );
    }

    #[Test]
    public function testSerializesFlatWireJson(): void
    {
        $auth = new SharedAuth(
            new Identity(id: '1', name: 'Ada', email: 'a@x.io', avatarUrl: '/a.png', roles: ['admin']),
            ['x:manage', 'x:view'],
            '/logout',
        );

        self::assertSame(
            '{"id":"1","name":"Ada","email":"a@x.io","avatarUrl":"\/a.png","roles":["admin"],'
            . '"capabilities":["x:manage","x:view"],"logoutUrl":"\/logout"}',
            json_encode($auth),
        );
        $this->assertValidAgainst('SharedAuth', $auth);
    }

    #[Test]
    public function testRejectsNonStringCapabilities(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new ReflectionClass(SharedAuth::class))->newInstanceArgs([new Identity(id: '1', name: 'Ada'), [1]]);
    }

    #[Test]
    public function testRejectsANonListCapabilityMap(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new ReflectionClass(SharedAuth::class))->newInstanceArgs([new Identity(id: '1', name: 'Ada'), ['x:view' => true]]);
    }

    #[Test]
    public function testSchemaIsFlatWithRequiredIdNameCapabilities(): void
    {
        $schema = SharedAuth::jsonSchema();

        self::assertSame(['id', 'name', 'capabilities'], $schema['required']);
        self::assertFalse($schema['additionalProperties']);
        self::assertArrayNotHasKey('allOf', $schema);
        self::assertSame(['type' => 'string'], $schema['properties']['id']);
    }

    #[Test]
    public function testSchemaRejectsAnIntegerIdAndMissingCapabilities(): void
    {
        $this->assertInvalidAgainst('SharedAuth', ['id' => 1, 'name' => 'Ada', 'capabilities' => []]);
        $this->assertInvalidAgainst('SharedAuth', ['id' => '1', 'name' => 'Ada']);
        $this->assertInvalidAgainst('SharedAuth', ['id' => '1', 'name' => 'Ada', 'capabilities' => ['a'], 'email' => null]);
    }
}

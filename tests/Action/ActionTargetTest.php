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

use Middag\Ui\Action\ActionTarget;
use Middag\Ui\Shared\Enum\ActionTargetKind;
use Middag\Ui\Shared\Enum\HttpMethod;
use Middag\Ui\Tests\Support\ValidatesAgainstSchema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @internal
 */
#[CoversClass(ActionTarget::class)]
final class ActionTargetTest extends TestCase
{
    use ValidatesAgainstSchema;

    #[Test]
    public function testIsReadonlyClass(): void
    {
        self::assertTrue((new ReflectionClass(ActionTarget::class))->isReadOnly());
    }

    #[Test]
    public function testLinkMinimal(): void
    {
        $target = ActionTarget::link('/users/1');

        self::assertSame(ActionTargetKind::Link, $target->kind);
        self::assertSame(['kind' => 'link', 'href' => '/users/1'], $target->jsonSerialize());
    }

    #[Test]
    public function testLinkExternal(): void
    {
        $payload = ActionTarget::link('https://x', external: true)->jsonSerialize();

        self::assertSame(['kind' => 'link', 'href' => 'https://x', 'external' => true], $payload);
    }

    #[Test]
    public function testRouteMinimal(): void
    {
        $payload = ActionTarget::route('users.edit')->jsonSerialize();

        self::assertSame(['kind' => 'route', 'route' => 'users.edit'], $payload);
        self::assertArrayNotHasKey('params', $payload);
    }

    #[Test]
    public function testRouteWithParams(): void
    {
        $payload = ActionTarget::route('users.edit', ['id' => 7])->jsonSerialize();

        self::assertSame(['kind' => 'route', 'route' => 'users.edit', 'params' => ['id' => 7]], $payload);
    }

    #[Test]
    public function testRequestDefaultsToPost(): void
    {
        $target = ActionTarget::request('/users/1');

        self::assertSame(HttpMethod::Post, $target->method);
        self::assertSame(['kind' => 'request', 'endpoint' => '/users/1', 'method' => 'post'], $target->jsonSerialize());
    }

    #[Test]
    public function testPanelCarriesOnlyKind(): void
    {
        $target = ActionTarget::panel();

        self::assertSame(ActionTargetKind::Panel, $target->kind);
        self::assertSame(['kind' => 'panel'], $target->jsonSerialize());
    }

    #[Test]
    public function testRequestWithMethod(): void
    {
        $payload = ActionTarget::request('/users/1', HttpMethod::Delete)->jsonSerialize();

        self::assertSame('delete', $payload['method']);
    }

    #[Test]
    public function testSchemaAcceptsEachSerializedBranch(): void
    {
        $this->assertValidAgainst('ActionTarget', ActionTarget::link('/users/1'));
        $this->assertValidAgainst('ActionTarget', ActionTarget::link('https://x', external: true));
        $this->assertValidAgainst('ActionTarget', ActionTarget::route('users.edit'));
        $this->assertValidAgainst('ActionTarget', ActionTarget::route('users.edit', ['id' => 7]));
        $this->assertValidAgainst('ActionTarget', ActionTarget::request('/users/1', HttpMethod::Delete));
    }

    #[Test]
    public function testSchemaRejectsAnUnknownKind(): void
    {
        $this->assertInvalidAgainst('ActionTarget', ['kind' => 'popup', 'href' => '/x']);
    }

    #[Test]
    public function testSchemaRejectsALinkMissingItsHref(): void
    {
        $this->assertInvalidAgainst('ActionTarget', ['kind' => 'link']);
    }

    #[Test]
    public function testSchemaRejectsAnAdditionalPropertyOnALinkBranch(): void
    {
        $this->assertInvalidAgainst('ActionTarget', ['kind' => 'link', 'href' => '/x', 'route' => 'y']);
    }

    #[Test]
    public function testSchemaRejectsExternalFalseSinceOnlyTrueIsEmitted(): void
    {
        $this->assertInvalidAgainst('ActionTarget', ['kind' => 'link', 'href' => '/x', 'external' => false]);
    }
}

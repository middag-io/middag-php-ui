<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Tests\Navigation;

use InvalidArgumentException;
use Middag\Ui\Navigation\NavigationNode;
use Middag\Ui\Navigation\NavigationTree;
use Middag\Ui\Tests\Support\ValidatesAgainstSchema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @internal
 */
#[CoversClass(NavigationTree::class)]
final class NavigationTreeTest extends TestCase
{
    use ValidatesAgainstSchema;

    #[Test]
    public function testIsReadonlyClass(): void
    {
        self::assertTrue((new ReflectionClass(NavigationTree::class))->isReadOnly());
    }

    #[Test]
    public function testEmptyTreeEmitsEveryKey(): void
    {
        self::assertSame('{"tree":[],"activeKey":"","footer":[]}', json_encode(new NavigationTree()));
    }

    #[Test]
    public function testSerializesExactWireJson(): void
    {
        $tree = new NavigationTree(
            tree: [new NavigationNode(key: 'home', label: 'Home', href: '/', active: true)],
            activeKey: 'home',
            footer: [new NavigationNode(key: 'help', label: 'Help', target: '_blank')],
        );

        self::assertSame(
            '{"tree":[{"key":"home","label":"Home","href":"\/","active":true}],"activeKey":"home",'
            . '"footer":[{"key":"help","label":"Help","target":"_blank"}]}',
            json_encode($tree),
        );
    }

    #[Test]
    public function testRejectsNonNodeItemsInTree(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new ReflectionClass(NavigationTree::class))->newInstanceArgs(['tree' => ['nope']]);
    }

    #[Test]
    public function testRejectsNonNodeItemsInFooter(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new ReflectionClass(NavigationTree::class))->newInstanceArgs(['footer' => [['key' => 'x']]]);
    }

    #[Test]
    public function testRejectsANonListTree(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new ReflectionClass(NavigationTree::class))->newInstanceArgs(['tree' => ['a' => new NavigationNode(key: 'a', label: 'A')]]);
    }

    #[Test]
    public function testSchemaDeclaresRequiredKeysAndNoDrilldownStack(): void
    {
        $schema = NavigationTree::jsonSchema();

        self::assertSame(['tree', 'activeKey', 'footer'], $schema['required']);
        self::assertFalse($schema['additionalProperties']);
        self::assertArrayNotHasKey('drilldownStack', $schema['properties']);
        self::assertSame(['type' => 'string'], $schema['properties']['activeKey']);
    }

    #[Test]
    public function testSchemaAcceptsASerializedTree(): void
    {
        $this->assertValidAgainst('NavigationTree', new NavigationTree());
        $this->assertValidAgainst('NavigationTree', new NavigationTree(
            tree: [new NavigationNode(key: 'a', label: 'A', children: [new NavigationNode(key: 'a.b', label: 'B')])],
            activeKey: 'a.b',
        ));
    }

    #[Test]
    public function testSchemaRejectsAnUnknownKey(): void
    {
        $this->assertInvalidAgainst('NavigationTree', ['tree' => [], 'activeKey' => '', 'footer' => [], 'drilldownStack' => []]);
    }

    #[Test]
    public function testSchemaRejectsAMissingActiveKey(): void
    {
        $this->assertInvalidAgainst('NavigationTree', ['tree' => [], 'footer' => []]);
        $this->assertInvalidAgainst('NavigationTree', ['tree' => [], 'activeKey' => null, 'footer' => []]);
    }
}

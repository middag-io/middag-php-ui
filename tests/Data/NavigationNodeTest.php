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

use Middag\Ui\Data\NavigationNode;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(NavigationNode::class)]
final class NavigationNodeTest extends TestCase
{
    #[Test]
    public function testDefaultValues(): void
    {
        $node = new NavigationNode(key: 'home', label: 'Home');

        self::assertSame('home', $node->key);
        self::assertSame('Home', $node->label);
        self::assertNull($node->icon);
        self::assertNull($node->href);
        self::assertNull($node->badge);
        self::assertFalse($node->active);
        self::assertFalse($node->drilldown);
        self::assertFalse($node->collapsible);
        self::assertFalse($node->default_open);
        self::assertSame([], $node->children);
        self::assertSame(50, $node->weight);
        self::assertNull($node->capability);
    }

    #[Test]
    public function testJsonSerializeMinimalOmitsEmptyKeys(): void
    {
        $node = new NavigationNode(key: 'home', label: 'Home');

        $payload = $node->jsonSerialize();

        // Consistent with every other VO: null/false/empty keys are omitted.
        self::assertSame(['key' => 'home', 'label' => 'Home'], $payload);
        self::assertArrayNotHasKey('icon', $payload);
        self::assertArrayNotHasKey('active', $payload);
        self::assertArrayNotHasKey('defaultOpen', $payload);
        self::assertArrayNotHasKey('children', $payload);
    }

    #[Test]
    public function testJsonSerializeWithAllFields(): void
    {
        $node = new NavigationNode(
            key: 'audience.segments',
            label: 'Segments',
            icon: 'users',
            href: '/segments',
            badge: '12',
            active: true,
            drilldown: true,
            collapsible: true,
            default_open: true,
            children: [],
            weight: 10,
            capability: 'local/middag:manage_segment',
        );

        $payload = $node->jsonSerialize();

        self::assertSame('audience.segments', $payload['key']);
        self::assertSame('Segments', $payload['label']);
        self::assertSame('users', $payload['icon']);
        self::assertSame('/segments', $payload['href']);
        self::assertSame('12', $payload['badge']);
        self::assertTrue($payload['active']);
        self::assertTrue($payload['drilldown']);
        self::assertTrue($payload['collapsible']);
        self::assertTrue($payload['defaultOpen']);
    }

    #[Test]
    public function testChildrenAreRecursivelySerializedAsArrays(): void
    {
        $child = new NavigationNode(key: 'child', label: 'Child', href: '/child');
        $parent = new NavigationNode(key: 'parent', label: 'Parent', children: [$child]);

        $payload = $parent->jsonSerialize();

        self::assertCount(1, $payload['children']);
        self::assertIsArray($payload['children'][0]);
        self::assertSame('child', $payload['children'][0]['key']);
        self::assertSame('Child', $payload['children'][0]['label']);
        self::assertSame('/child', $payload['children'][0]['href']);
    }

    #[Test]
    public function testDeepChildrenNested(): void
    {
        $grandchild = new NavigationNode(key: 'gc', label: 'Grandchild');
        $child = new NavigationNode(key: 'c', label: 'Child', children: [$grandchild]);
        $root = new NavigationNode(key: 'r', label: 'Root', children: [$child]);

        $payload = $root->jsonSerialize();

        self::assertCount(1, $payload['children']);
        self::assertCount(1, $payload['children'][0]['children']);
        self::assertSame('gc', $payload['children'][0]['children'][0]['key']);
    }

    #[Test]
    public function testDefaultOpenIsSerializedAsCamelCase(): void
    {
        $node = new NavigationNode(key: 'n', label: 'N', default_open: true);
        $payload = $node->jsonSerialize();

        self::assertArrayHasKey('defaultOpen', $payload);
        self::assertArrayNotHasKey('default_open', $payload);
        self::assertTrue($payload['defaultOpen']);
    }

    #[Test]
    public function testJsonEncodeProducesValidJson(): void
    {
        $node = new NavigationNode(key: 'home', label: 'Home', href: '/');

        $json = json_encode($node, JSON_THROW_ON_ERROR);

        self::assertJson($json);

        $decoded = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        self::assertSame('home', $decoded['key']);
    }
}

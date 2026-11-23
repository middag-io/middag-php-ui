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
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
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
        self::assertFalse($node->defaultOpen);
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
            defaultOpen: true,
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
        $node = new NavigationNode(key: 'n', label: 'N', defaultOpen: true);
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

    #[Test]
    public function testJsonSchemaDeclaresTheRequiredWireFields(): void
    {
        $schema = NavigationNode::jsonSchema();

        self::assertSame('object', $schema['type']);
        self::assertSame(['key', 'label'], $schema['required']);
        self::assertFalse($schema['additionalProperties']);
    }

    #[Test]
    public function testNewOptionalFieldsDefaultToNullAndAreOmitted(): void
    {
        $node = new NavigationNode(key: 'home', label: 'Home');

        self::assertNull($node->entityType);
        self::assertNull($node->badgeVariant);
        self::assertNull($node->statusColor);
        self::assertNull($node->target);
        self::assertSame('{"key":"home","label":"Home"}', json_encode($node));
    }

    #[Test]
    public function testNewOptionalFieldsAreEmittedWhenSet(): void
    {
        $node = new NavigationNode(
            key: 'inbox',
            label: 'Inbox',
            badge: '3',
            entityType: 'course',
            badgeVariant: 'alert',
            statusColor: 'green',
            target: '_blank',
        );

        self::assertSame(
            '{"key":"inbox","label":"Inbox","badge":"3","entityType":"course","badgeVariant":"alert",'
            . '"statusColor":"green","target":"_blank"}',
            json_encode($node),
        );
    }

    #[Test]
    public function testSchemaTypesTheNewFieldsAsOpenStrings(): void
    {
        $properties = NavigationNode::jsonSchema()['properties'];

        foreach (['entityType', 'badgeVariant', 'statusColor', 'target'] as $field) {
            self::assertSame(['type' => 'string'], $properties[$field]);
        }
    }

    #[Test]
    #[DataProvider('unsafeHrefs')]
    public function testRejectsAnUnsafeHrefScheme(string $href): void
    {
        $this->expectException(InvalidArgumentException::class);

        new NavigationNode(key: 'x', label: 'X', href: $href);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function unsafeHrefs(): iterable
    {
        yield 'javascript' => ['javascript:alert(1)'];

        yield 'mixed case with leading space' => [' JavaScript:alert(1)'];

        yield 'tab inside the scheme' => ["java\tscript:alert(1)"];

        yield 'control character prefix' => ["\x01javascript:alert(1)"];

        yield 'data' => ['data:text/plain;base64,SGk='];

        yield 'vbscript' => ['VBScript:msgbox(1)'];
    }

    #[Test]
    #[DataProvider('safeHrefs')]
    public function testAcceptsASafeHref(string $href): void
    {
        self::assertSame($href, (new NavigationNode(key: 'x', label: 'X', href: $href))->jsonSerialize()['href']);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function safeHrefs(): iterable
    {
        yield 'root-relative' => ['/courses'];

        yield 'http' => ['http://example.test/a'];

        yield 'https' => ['https://docs.example/guide'];

        yield 'relative path that mentions data' => ['/data:export'];
    }
}

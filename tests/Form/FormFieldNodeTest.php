<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Tests\Form;

use Middag\Ui\Form\Contract\FormSchemaNodeInterface;
use Middag\Ui\Form\FormFieldNode;
use Middag\Ui\Shared\Enum\FormComponent;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @internal
 */
#[CoversClass(FormFieldNode::class)]
final class FormFieldNodeTest extends TestCase
{
    #[Test]
    public function testIsReadonlyNode(): void
    {
        $ref = new ReflectionClass(FormFieldNode::class);

        self::assertTrue($ref->isReadOnly());
        self::assertTrue($ref->implementsInterface(FormSchemaNodeInterface::class));
    }

    #[Test]
    public function testSerializesNodeShape(): void
    {
        $payload = (new FormFieldNode('email', FormComponent::Email, [
            'label' => 'Email',
            'required' => true,
        ]))->jsonSerialize();

        self::assertSame('field', $payload['kind']);
        self::assertSame('email', $payload['key']);
        self::assertSame('email', $payload['component']);
        self::assertEquals((object) ['label' => 'Email', 'required' => true], $payload['props']);
    }

    #[Test]
    public function testUsesEnumValueForComponent(): void
    {
        $payload = (new FormFieldNode('range', FormComponent::DateRange, ['label' => 'When']))->jsonSerialize();

        self::assertSame('date_range', $payload['component']);
    }

    #[Test]
    public function testEmptyPropsSerializeToJsonObject(): void
    {
        $payload = (new FormFieldNode('x', FormComponent::Text))->jsonSerialize();

        // An empty props bag must encode as `{}`, never `[]` — the renderer
        // always reads props as an object.
        self::assertSame('{}', json_encode($payload['props']));
    }

    #[Test]
    public function testJsonSchemaIsADiscriminatedUnion(): void
    {
        $schema = FormFieldNode::jsonSchema();

        self::assertArrayHasKey('oneOf', $schema);
        self::assertIsArray($schema['oneOf']);
        // 23 closed branches mirroring the @middag-io/react field interfaces.
        self::assertCount(23, $schema['oneOf']);
    }
}

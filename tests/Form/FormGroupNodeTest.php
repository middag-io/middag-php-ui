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

use Middag\Ui\Form\FormFieldNode;
use Middag\Ui\Form\FormGroupNode;
use Middag\Ui\Shared\Enum\FormComponent;
use Middag\Ui\Tests\Support\ValidatesAgainstSchema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(FormGroupNode::class)]
final class FormGroupNodeTest extends TestCase
{
    use ValidatesAgainstSchema;

    #[Test]
    public function testSerializesWithColumns(): void
    {
        $payload = (new FormGroupNode(
            id: 'name_row',
            children: [new FormFieldNode('first', FormComponent::TEXT, ['label' => 'First'])],
            columns: 2,
        ))->jsonSerialize();

        self::assertSame('group', $payload['kind']);
        self::assertSame('name_row', $payload['id']);
        self::assertSame(2, $payload['columns']);
        self::assertCount(1, $payload['children']);
    }

    #[Test]
    public function testOmitsColumnsWhenNull(): void
    {
        $payload = (new FormGroupNode(id: 'g'))->jsonSerialize();

        self::assertArrayNotHasKey('columns', $payload);
        self::assertSame([], $payload['children']);
    }

    #[Test]
    public function testSchemaAcceptsAGroupWithColumnsAndChildren(): void
    {
        $this->assertValidAgainst('FormGroupNode', new FormGroupNode(
            id: 'name_row',
            children: [new FormFieldNode('first', FormComponent::TEXT, ['label' => 'First'])],
            columns: 2,
        ));
    }

    #[Test]
    public function testSchemaAcceptsAnEmptyGroupWithoutColumns(): void
    {
        $this->assertValidAgainst('FormGroupNode', new FormGroupNode(id: 'g'));
    }

    #[Test]
    public function testSchemaRejectsAGroupMissingChildren(): void
    {
        $this->assertInvalidAgainst('FormGroupNode', ['kind' => 'group', 'id' => 'g']);
    }

    #[Test]
    public function testSchemaRejectsAGroupWithAWrongKind(): void
    {
        $this->assertInvalidAgainst('FormGroupNode', ['kind' => 'section', 'id' => 'g', 'children' => []]);
    }

    #[Test]
    public function testSchemaRejectsAGroupWithColumnsOutOfEnum(): void
    {
        $this->assertInvalidAgainst('FormGroupNode', ['kind' => 'group', 'id' => 'g', 'children' => [], 'columns' => 3]);
    }

    #[Test]
    public function testSchemaRejectsAnAdditionalPropertyOnAGroup(): void
    {
        $this->assertInvalidAgainst('FormGroupNode', ['kind' => 'group', 'id' => 'g', 'children' => [], 'evil' => 1]);
    }
}

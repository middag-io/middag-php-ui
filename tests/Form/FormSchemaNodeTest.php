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
use Middag\Ui\Form\FormHeaderNode;
use Middag\Ui\Form\FormSchemaNode;
use Middag\Ui\Form\FormSectionNode;
use Middag\Ui\Shared\Enum\FormComponent;
use Middag\Ui\Tests\Support\ValidatesAgainstSchema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;

/**
 * @internal
 */
#[CoversClass(FormSchemaNode::class)]
final class FormSchemaNodeTest extends TestCase
{
    use ValidatesAgainstSchema;

    #[Test]
    public function testSchemaAcceptsTheFieldBranch(): void
    {
        $this->assertValidAgainst('FormSchemaNode', new FormFieldNode('a', FormComponent::Text, ['label' => 'A']));
    }

    #[Test]
    public function testSchemaAcceptsTheHeaderBranch(): void
    {
        $this->assertValidAgainst('FormSchemaNode', new FormHeaderNode('Header'));
    }

    #[Test]
    public function testSchemaAcceptsTheGroupBranch(): void
    {
        $this->assertValidAgainst('FormSchemaNode', new FormGroupNode('g', [
            new FormFieldNode('a', FormComponent::Text, ['label' => 'A']),
        ], 2));
    }

    #[Test]
    public function testSchemaAcceptsTheSectionBranchAndRecurses(): void
    {
        $this->assertValidAgainst('FormSchemaNode', new FormSectionNode('sec', 'Section', [
            new FormHeaderNode('Sub header'),
            new FormFieldNode('bio', FormComponent::Textarea, ['label' => 'Bio']),
        ], true));
    }

    #[Test]
    public function testSchemaRejectsAnUnknownNodeKind(): void
    {
        $this->assertInvalidAgainst('FormSchemaNode', ['kind' => 'bogus']);
    }

    #[Test]
    public function testSchemaRejectsAFieldNodeMissingItsIdentity(): void
    {
        $this->assertInvalidAgainst('FormSchemaNode', ['kind' => 'field']);
    }

    /**
     * The umbrella is a schema-only union with no instances; its private
     * constructor is exercised via reflection so the ctor is covered.
     */
    #[Test]
    public function testPrivateConstructorIsInvocable(): void
    {
        $constructor = new ReflectionMethod(FormSchemaNode::class, '__construct');
        self::assertTrue($constructor->isPrivate());

        $instance = (new ReflectionClass(FormSchemaNode::class))->newInstanceWithoutConstructor();
        $constructor->invoke($instance);
    }
}

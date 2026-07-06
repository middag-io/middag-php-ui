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

use Middag\Ui\Form\FormHeaderNode;
use Middag\Ui\Tests\Support\ValidatesAgainstSchema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(FormHeaderNode::class)]
final class FormHeaderNodeTest extends TestCase
{
    use ValidatesAgainstSchema;

    #[Test]
    public function testSerializesHeaderShape(): void
    {
        $payload = (new FormHeaderNode('Contact details'))->jsonSerialize();

        self::assertSame(['kind' => 'header', 'label' => 'Contact details'], $payload);
    }

    #[Test]
    public function testSchemaAcceptsAHeaderNode(): void
    {
        $this->assertValidAgainst('FormHeaderNode', new FormHeaderNode('Contact details'));
    }

    #[Test]
    public function testSchemaRejectsAHeaderMissingItsLabel(): void
    {
        $this->assertInvalidAgainst('FormHeaderNode', ['kind' => 'header']);
    }

    #[Test]
    public function testSchemaRejectsAHeaderWithAWrongKind(): void
    {
        $this->assertInvalidAgainst('FormHeaderNode', ['kind' => 'group', 'label' => 'X']);
    }

    #[Test]
    public function testSchemaRejectsAnAdditionalPropertyOnAHeader(): void
    {
        $this->assertInvalidAgainst('FormHeaderNode', ['kind' => 'header', 'label' => 'X', 'evil' => 1]);
    }
}

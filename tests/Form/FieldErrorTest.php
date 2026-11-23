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

use Middag\Ui\Form\FieldError;
use Middag\Ui\Form\FieldMessage;
use Middag\Ui\Tests\Support\ValidatesAgainstSchema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @internal
 */
#[CoversClass(FieldError::class)]
final class FieldErrorTest extends TestCase
{
    use ValidatesAgainstSchema;

    #[Test]
    public function testIsSchemaOnlyWithPrivateConstructor(): void
    {
        $ctor = (new ReflectionClass(FieldError::class))->getConstructor();

        self::assertNotNull($ctor);
        self::assertTrue($ctor->isPrivate());

        // Invoke the (empty) private constructor via reflection purely to prove
        // it is inert; instances are never used at runtime — this is a
        // schema-only value shape.
        $instance = (new ReflectionClass(FieldError::class))->newInstanceWithoutConstructor();
        $ctor->invoke($instance);
        self::assertInstanceOf(FieldError::class, $instance);
    }

    #[Test]
    public function testAcceptsASingleMessageString(): void
    {
        $this->assertValidAgainst('FieldError', 'This field is required.');
    }

    #[Test]
    public function testAcceptsAListOfMessages(): void
    {
        $this->assertValidAgainst('FieldError', ['Too short.', 'Must be a number.']);
    }

    #[Test]
    public function testAcceptsAStructuredMessage(): void
    {
        $this->assertValidAgainst('FieldError', new FieldMessage('Invalid email.', 'validation.email', 'validators'));
    }

    #[Test]
    public function testAcceptsAMixedListOfMessagesAndStructuredMessages(): void
    {
        $this->assertValidAgainst('FieldError', [
            'Too many tags',
            (new FieldMessage('Tags must be unique.', 'validation.unique', 'validators', ['limit' => 3]))->jsonSerialize(),
        ]);
    }

    #[Test]
    public function testRejectsAnIncompleteStructuredMessage(): void
    {
        $this->assertInvalidAgainst('FieldError', ['message' => 'x', 'key' => 'k']);
    }

    #[Test]
    public function testRejectsANonStringScalar(): void
    {
        $this->assertInvalidAgainst('FieldError', 42);
    }

    #[Test]
    public function testRejectsAListContainingNonStrings(): void
    {
        $this->assertInvalidAgainst('FieldError', ['ok', 7]);
    }
}

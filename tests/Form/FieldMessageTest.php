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

use InvalidArgumentException;
use Middag\Ui\Form\FieldMessage;
use Middag\Ui\Tests\Support\ValidatesAgainstSchema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @internal
 */
#[CoversClass(FieldMessage::class)]
final class FieldMessageTest extends TestCase
{
    use ValidatesAgainstSchema;

    #[Test]
    public function testIsFinalReadonly(): void
    {
        $class = new ReflectionClass(FieldMessage::class);

        self::assertTrue($class->isFinal());
        self::assertTrue($class->isReadOnly());
    }

    #[Test]
    public function testOmitsEmptyParams(): void
    {
        $message = new FieldMessage('Required.', 'validation.required', 'validators');

        self::assertSame(
            '{"message":"Required.","key":"validation.required","domain":"validators"}',
            json_encode($message),
        );
        $this->assertValidAgainst('FieldMessage', $message);
    }

    #[Test]
    public function testKeepsParams(): void
    {
        $message = new FieldMessage('Too many tags.', 'validation.max', 'validators', ['limit' => 3]);

        self::assertSame(
            ['message' => 'Too many tags.', 'key' => 'validation.max', 'domain' => 'validators', 'params' => ['limit' => 3]],
            $message->jsonSerialize(),
        );
        $this->assertValidAgainst('FieldMessage', $message);
    }

    #[Test]
    public function testConstructorRejectsAListOfParams(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new FieldMessage('x', 'k', 'd', ['a']);
    }

    #[Test]
    public function testSchemaRejectsAMissingKey(): void
    {
        $this->assertInvalidAgainst('FieldMessage', ['message' => 'x', 'domain' => 'd']);
    }

    #[Test]
    public function testSchemaRejectsAnExtraProperty(): void
    {
        $this->assertInvalidAgainst('FieldMessage', ['message' => 'x', 'key' => 'k', 'domain' => 'd', 'extra' => 1]);
    }

    #[Test]
    public function testSchemaRejectsParamsAsAJsonArray(): void
    {
        $this->assertInvalidAgainst('FieldMessage', ['message' => 'x', 'key' => 'k', 'domain' => 'd', 'params' => []]);
    }
}

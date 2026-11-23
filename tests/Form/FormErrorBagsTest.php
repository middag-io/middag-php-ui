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
use Middag\Ui\Form\FormErrorBags;
use Middag\Ui\Form\FormErrors;
use Middag\Ui\Tests\Support\ValidatesAgainstSchema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @internal
 */
#[CoversClass(FormErrorBags::class)]
final class FormErrorBagsTest extends TestCase
{
    use ValidatesAgainstSchema;

    #[Test]
    public function testIsFinalReadonly(): void
    {
        $class = new ReflectionClass(FormErrorBags::class);

        self::assertTrue($class->isFinal());
        self::assertTrue($class->isReadOnly());
    }

    #[Test]
    public function testEmptySerializesAsAnObject(): void
    {
        self::assertSame('{}', json_encode(new FormErrorBags()));
        $this->assertValidAgainst('FormErrorBags', new FormErrorBags());
    }

    #[Test]
    public function testSerializesEachBagThroughFormErrors(): void
    {
        $bags = new FormErrorBags([
            'login' => new FormErrors(['email' => new FieldMessage('Invalid email.', 'validation.email', 'validators')]),
            'signup' => new FormErrors(),
        ]);

        self::assertSame(
            '{"login":{"email":{"message":"Invalid email.","key":"validation.email","domain":"validators"}},"signup":{}}',
            json_encode($bags),
        );
        $this->assertValidAgainst('FormErrorBags', $bags);
    }

    #[Test]
    public function testANumericBagNameStillSerializesAsAnObject(): void
    {
        $bags = new FormErrorBags(['0' => new FormErrors(['name' => 'Required'])]);

        self::assertSame('{"0":{"name":"Required"}}', json_encode($bags));
        $this->assertValidAgainst('FormErrorBags', $bags);
    }

    #[Test]
    public function testRejectsAnEmptyBagName(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new FormErrorBags(['' => new FormErrors()]);
    }

    #[Test]
    public function testRejectsAValueThatIsNotFormErrors(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new FormErrorBags(['login' => ['email' => 'Required']]);
    }

    #[Test]
    public function testSchemaRejectsAnInvalidBagValue(): void
    {
        $this->assertInvalidAgainst('FormErrorBags', ['b' => ['f' => 42]]);
    }
}

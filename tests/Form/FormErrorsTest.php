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

use Middag\Ui\Form\FormErrors;
use Middag\Ui\Tests\Support\ValidatesAgainstSchema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @internal
 */
#[CoversClass(FormErrors::class)]
final class FormErrorsTest extends TestCase
{
    use ValidatesAgainstSchema;

    #[Test]
    public function testIsReadonlyClass(): void
    {
        self::assertTrue((new ReflectionClass(FormErrors::class))->isReadOnly());
    }

    #[Test]
    public function testIsEmptyByDefault(): void
    {
        self::assertSame([], (new FormErrors())->jsonSerialize());
    }

    #[Test]
    public function testSerializeReturnsTheErrorMapVerbatim(): void
    {
        $errors = ['email' => 'Invalid address.', 'name' => ['Required.', 'Too short.']];

        self::assertSame($errors, (new FormErrors($errors))->jsonSerialize());
    }

    #[Test]
    public function testFormLevelKeyIsTheReservedUnderscore(): void
    {
        self::assertSame('_', FormErrors::FORM_LEVEL_KEY);
    }

    #[Test]
    public function testSerializedMapValidatesWithFormLevelAndDottedKeys(): void
    {
        $this->assertValidAgainst('FormErrors', new FormErrors([
            FormErrors::FORM_LEVEL_KEY => 'The whole form failed.',
            'address.zip' => ['Required.'],
        ]));
    }

    #[Test]
    public function testSchemaRejectsANonStringErrorValue(): void
    {
        $this->assertInvalidAgainst('FormErrors', ['email' => 42]);
    }
}

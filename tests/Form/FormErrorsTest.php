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
        self::assertSame('{}', json_encode(new FormErrors()));
    }

    #[Test]
    public function testSerializeReturnsTheErrorMapVerbatim(): void
    {
        $errors = ['email' => 'Invalid address.', 'name' => ['Required.', 'Too short.']];

        self::assertSame(json_encode($errors), json_encode(new FormErrors($errors)));
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
    public function testSerializeNormalizesStructuredMessagesAloneAndInLists(): void
    {
        $email = new FieldMessage('Invalid email.', 'validation.email', 'validators');
        $unique = new FieldMessage('Tags must be unique.', 'validation.unique', 'validators', ['limit' => 3]);

        $errors = new FormErrors([
            'email' => $email,
            'tags' => ['Too many tags', $unique],
            FormErrors::FORM_LEVEL_KEY => 'The form could not be saved.',
        ]);

        self::assertSame(
            '{"email":{"message":"Invalid email.","key":"validation.email","domain":"validators"},'
            . '"tags":["Too many tags",{"message":"Tags must be unique.","key":"validation.unique","domain":"validators","params":{"limit":3}}],'
            . '"_":"The form could not be saved."}',
            json_encode($errors),
        );
        $this->assertValidAgainst('FormErrors', $errors);
    }

    #[Test]
    public function testSerializedSessionArraysRoundTrip(): void
    {
        $fromSession = [
            'email' => ['message' => 'Invalid email.', 'key' => 'validation.email', 'domain' => 'validators'],
            'tags' => ['Too many tags', ['message' => 'Unique.', 'key' => 'validation.unique', 'domain' => 'validators', 'params' => ['limit' => 3]]],
        ];

        $errors = new FormErrors($fromSession);

        self::assertSame(json_encode($fromSession), json_encode($errors));
        $this->assertValidAgainst('FormErrors', $errors);
    }

    #[Test]
    public function testStructuredArraysDropAnEmptyParams(): void
    {
        $raw = ['message' => 'm', 'key' => 'k', 'domain' => 'd', 'params' => []];

        $errors = new FormErrors(['a' => $raw, 'b' => ['Plain.', $raw]]);

        self::assertSame(
            '{"a":{"message":"m","key":"k","domain":"d"},"b":["Plain.",{"message":"m","key":"k","domain":"d"}]}',
            json_encode($errors),
        );
        $this->assertValidAgainst('FormErrors', $errors);
    }

    #[Test]
    public function testStructuredArrayWithListParamsIsRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new FormErrors(['a' => ['message' => 'm', 'key' => 'k', 'domain' => 'd', 'params' => ['x']]]))->jsonSerialize();
    }

    #[Test]
    public function testStructuredArrayMissingAKeyIsRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new FormErrors(['a' => ['message' => 'm', 'key' => 'k']]))->jsonSerialize();
    }

    #[Test]
    public function testSequentialNumericFieldNamesStayAnObject(): void
    {
        $errors = new FormErrors(['0' => 'x', '1' => 'y']);

        self::assertSame('{"0":"x","1":"y"}', json_encode($errors));
        $this->assertValidAgainst('FormErrors', $errors);
    }

    #[Test]
    public function testSchemaRejectsANonStringErrorValue(): void
    {
        $this->assertInvalidAgainst('FormErrors', ['email' => 42]);
    }
}

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

use Middag\Ui\Form\FormFieldDocumentMask;
use Middag\Ui\Tests\Support\ValidatesAgainstSchema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @internal
 */
#[CoversClass(FormFieldDocumentMask::class)]
final class FormFieldDocumentMaskTest extends TestCase
{
    use ValidatesAgainstSchema;

    #[Test]
    public function testIsSchemaOnlyWithPrivateConstructor(): void
    {
        $ctor = (new ReflectionClass(FormFieldDocumentMask::class))->getConstructor();

        self::assertNotNull($ctor);
        self::assertTrue($ctor->isPrivate());

        // Invoke the (empty) private constructor via reflection purely to prove
        // it is inert; instances are never used at runtime — this is a
        // schema-only value shape.
        $instance = (new ReflectionClass(FormFieldDocumentMask::class))->newInstanceWithoutConstructor();
        $ctor->invoke($instance);
        self::assertInstanceOf(FormFieldDocumentMask::class, $instance);
    }

    #[Test]
    public function testAcceptsAMaskWithItsRequiredPatternAndMaxLength(): void
    {
        $this->assertValidAgainst('FormFieldDocumentMask', ['pattern' => '999.999.999-99', 'maxLength' => 14]);
    }

    #[Test]
    public function testAcceptsAnOptionalPlaceholder(): void
    {
        $this->assertValidAgainst('FormFieldDocumentMask', [
            'pattern' => '99.999.999/9999-99',
            'maxLength' => 18,
            'placeholder' => '00.000.000/0000-00',
        ]);
    }

    #[Test]
    public function testRejectsAMaskMissingMaxLength(): void
    {
        $this->assertInvalidAgainst('FormFieldDocumentMask', ['pattern' => '999.999.999-99']);
    }

    #[Test]
    public function testRejectsAnAdditionalProperty(): void
    {
        $this->assertInvalidAgainst('FormFieldDocumentMask', ['pattern' => '9', 'maxLength' => 14, 'evil' => 1]);
    }
}

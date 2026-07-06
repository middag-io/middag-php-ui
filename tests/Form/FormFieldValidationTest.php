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

use Middag\Ui\Form\FormFieldValidation;
use Middag\Ui\Tests\Support\ValidatesAgainstSchema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @internal
 */
#[CoversClass(FormFieldValidation::class)]
final class FormFieldValidationTest extends TestCase
{
    use ValidatesAgainstSchema;

    #[Test]
    public function testIsSchemaOnlyWithPrivateConstructor(): void
    {
        $ctor = (new ReflectionClass(FormFieldValidation::class))->getConstructor();

        self::assertNotNull($ctor);
        self::assertTrue($ctor->isPrivate());
    }

    #[Test]
    public function testAcceptsAnEmptyObjectSinceNoRuleIsRequired(): void
    {
        // Cast so json_encode emits `{}` (empty object), not `[]` (empty array).
        $this->assertValidAgainst('FormFieldValidation', (object) []);
    }

    #[Test]
    public function testAcceptsEveryValidationRule(): void
    {
        $this->assertValidAgainst('FormFieldValidation', [
            'minLength' => 2,
            'maxLength' => 50,
            'pattern' => '^[a-z]+$',
            'patternMessage' => 'Lowercase letters only.',
        ]);
    }

    #[Test]
    public function testRejectsANonIntegerMinLength(): void
    {
        $this->assertInvalidAgainst('FormFieldValidation', ['minLength' => '2']);
    }

    #[Test]
    public function testRejectsAnAdditionalProperty(): void
    {
        $this->assertInvalidAgainst('FormFieldValidation', ['minLength' => 2, 'evil' => 1]);
    }
}

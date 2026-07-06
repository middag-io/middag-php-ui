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

use Middag\Ui\Form\FieldPropsBase;
use Middag\Ui\Tests\Support\ValidatesAgainstSchema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @internal
 */
#[CoversClass(FieldPropsBase::class)]
final class FieldPropsBaseTest extends TestCase
{
    use ValidatesAgainstSchema;

    #[Test]
    public function testIsSchemaOnlyWithPrivateConstructor(): void
    {
        $ctor = (new ReflectionClass(FieldPropsBase::class))->getConstructor();

        self::assertNotNull($ctor);
        self::assertTrue($ctor->isPrivate());
    }

    #[Test]
    public function testAcceptsMinimalPropsWithOnlyALabel(): void
    {
        $this->assertValidAgainst('FieldPropsBase', ['label' => 'Full name']);
    }

    #[Test]
    public function testRejectsPropsWithoutTheRequiredLabel(): void
    {
        $this->assertInvalidAgainst('FieldPropsBase', ['placeholder' => 'Type here']);
    }

    #[Test]
    public function testResolvesReferencedValidationAndConditionProps(): void
    {
        $this->assertValidAgainst('FieldPropsBase', [
            'label' => 'Secret',
            'validation' => ['minLength' => 8],
            'visible_when' => ['field' => 'type', 'operator' => 'equals', 'value' => 'b'],
        ]);
    }

    #[Test]
    public function testStaysOpenForAllOfCompositionByAllowingExtraProps(): void
    {
        // FieldPropsBase intentionally omits additionalProperties so it can be
        // intersected under allOf with component-specific props (D-04:A).
        $this->assertValidAgainst('FieldPropsBase', ['label' => 'X', 'options' => []]);
    }
}

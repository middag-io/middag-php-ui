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

use Middag\Ui\Form\FormCondition;
use Middag\Ui\Tests\Support\ValidatesAgainstSchema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @internal
 */
#[CoversClass(FormCondition::class)]
final class FormConditionTest extends TestCase
{
    use ValidatesAgainstSchema;

    #[Test]
    public function testIsSchemaOnlyWithPrivateConstructor(): void
    {
        $ctor = (new ReflectionClass(FormCondition::class))->getConstructor();

        self::assertNotNull($ctor);
        self::assertTrue($ctor->isPrivate());
    }

    #[Test]
    public function testAcceptsAnEqualsConditionWithAStringValue(): void
    {
        $this->assertValidAgainst('FormCondition', ['field' => 'type', 'operator' => 'equals', 'value' => 'b']);
    }

    #[Test]
    public function testAcceptsAnInConditionWithAListValue(): void
    {
        $this->assertValidAgainst('FormCondition', ['field' => 'tags', 'operator' => 'in', 'value' => ['a', 'b']]);
    }

    #[Test]
    public function testRejectsAnOperatorOutsideTheClientVocabulary(): void
    {
        // `contains` belongs to the richer server-side ConditionOperator catalog,
        // not the narrow client-evaluated set this wire condition allows.
        $this->assertInvalidAgainst('FormCondition', ['field' => 'x', 'operator' => 'contains', 'value' => 'y']);
    }

    #[Test]
    public function testRejectsAConditionMissingTheRequiredField(): void
    {
        $this->assertInvalidAgainst('FormCondition', ['operator' => 'equals', 'value' => 'b']);
    }

    #[Test]
    public function testRejectsAnAdditionalProperty(): void
    {
        $this->assertInvalidAgainst('FormCondition', ['field' => 'x', 'operator' => 'equals', 'value' => 'b', 'extra' => 1]);
    }
}

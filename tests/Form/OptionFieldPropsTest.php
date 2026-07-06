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

use Middag\Ui\Form\OptionFieldProps;
use Middag\Ui\Tests\Support\ValidatesAgainstSchema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @internal
 */
#[CoversClass(OptionFieldProps::class)]
final class OptionFieldPropsTest extends TestCase
{
    use ValidatesAgainstSchema;

    #[Test]
    public function testIsSchemaOnlyWithPrivateConstructor(): void
    {
        $ctor = (new ReflectionClass(OptionFieldProps::class))->getConstructor();

        self::assertNotNull($ctor);
        self::assertTrue($ctor->isPrivate());
    }

    #[Test]
    public function testAcceptsTheBasePropsPlusAnOptionsList(): void
    {
        $this->assertValidAgainst('OptionFieldProps', [
            'label' => 'Type',
            'options' => [
                ['value' => 'a', 'label' => 'Option A'],
                ['value' => 'b', 'label' => 'Option B'],
            ],
        ]);
    }

    #[Test]
    public function testRejectsPropsMissingTheLabelInheritedFromTheBase(): void
    {
        // The allOf intersection inherits FieldPropsBase's required `label`.
        $this->assertInvalidAgainst('OptionFieldProps', [
            'options' => [['value' => 'a', 'label' => 'Option A']],
        ]);
    }

    #[Test]
    public function testRejectsAMalformedOptionMissingItsLabel(): void
    {
        $this->assertInvalidAgainst('OptionFieldProps', [
            'label' => 'Type',
            'options' => [['value' => 'a']],
        ]);
    }
}

<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Tests\Shared\ValueObject;

use Middag\Ui\Shared\ValueObject\Label;
use Middag\Ui\Shared\ValueObject\Translatable;
use Middag\Ui\Tests\Support\ValidatesAgainstSchema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(Label::class)]
final class LabelTest extends TestCase
{
    use ValidatesAgainstSchema;

    #[Test]
    public function testSerializeStringPassesThrough(): void
    {
        self::assertSame('Raw', Label::serialize('Raw'));
    }

    #[Test]
    public function testSerializeTranslatableProducesIntent(): void
    {
        self::assertSame(
            ['key' => 'k', 'domain' => 'd'],
            Label::serialize(Translatable::of('k', 'd')),
        );
    }

    #[Test]
    public function testSerializeNullableReturnsNull(): void
    {
        self::assertNull(Label::serializeNullable(null));
    }

    #[Test]
    public function testSerializeNullableHandlesStringAndTranslatable(): void
    {
        self::assertSame('Raw', Label::serializeNullable('Raw'));
        self::assertSame(
            ['key' => 'k', 'domain' => 'd'],
            Label::serializeNullable(Translatable::of('k', 'd')),
        );
    }

    #[Test]
    public function testSchemaAcceptsAPlainStringLabel(): void
    {
        $this->assertValidAgainst('Label', 'Raw');
    }

    #[Test]
    public function testSchemaAcceptsATranslatableLabel(): void
    {
        $this->assertValidAgainst('Label', Translatable::of('k', 'd'));
    }

    #[Test]
    public function testSchemaRejectsANonStringNonTranslatableScalar(): void
    {
        $this->assertInvalidAgainst('Label', 42);
    }

    #[Test]
    public function testSchemaRejectsAnObjectMissingTheTranslatableKey(): void
    {
        $this->assertInvalidAgainst('Label', ['domain' => 'd']);
    }
}

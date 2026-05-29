<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Tests\Data;

use Middag\Ui\Data\Label;
use Middag\Ui\Data\Translatable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(Label::class)]
final class LabelTest extends TestCase
{
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
}

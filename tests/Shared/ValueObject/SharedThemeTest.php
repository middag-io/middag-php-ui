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

use InvalidArgumentException;
use Middag\Ui\Shared\Enum\ThemeMode;
use Middag\Ui\Shared\ValueObject\SharedTheme;
use Middag\Ui\Tests\Support\ValidatesAgainstSchema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @internal
 */
#[CoversClass(SharedTheme::class)]
final class SharedThemeTest extends TestCase
{
    use ValidatesAgainstSchema;

    #[Test]
    public function testIsReadonlyClass(): void
    {
        self::assertTrue((new ReflectionClass(SharedTheme::class))->isReadOnly());
    }

    #[Test]
    public function testEmptyThemeEmitsAnEmptyStringsObject(): void
    {
        self::assertSame('{"strings":{}}', json_encode(new SharedTheme()));
    }

    #[Test]
    public function testSerializesAppearanceAndStrings(): void
    {
        $theme = new SharedTheme(ThemeMode::Light, ['save' => 'Salvar']);

        self::assertSame('{"appearance":"light","strings":{"save":"Salvar"}}', json_encode($theme));
        $this->assertValidAgainst('SharedTheme', $theme);
    }

    #[Test]
    public function testRejectsNonStringStringValues(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new ReflectionClass(SharedTheme::class))->newInstanceArgs(['strings' => ['save' => 1]]);
    }

    #[Test]
    public function testSchemaRejectsBrandColorAndInherit(): void
    {
        $this->assertInvalidAgainst('SharedTheme', ['brandColor' => '#fff']);
        $this->assertInvalidAgainst('SharedTheme', ['inherit' => true]);
    }
}

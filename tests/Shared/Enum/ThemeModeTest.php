<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Tests\Shared\Enum;

use Middag\Ui\Shared\Enum\ThemeMode;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversNothing]
final class ThemeModeTest extends TestCase
{
    #[Test]
    public function allExpectedCasesExist(): void
    {
        $values = array_column(ThemeMode::cases(), 'value');

        self::assertSame(['light', 'dark', 'system'], $values);
    }

    #[Test]
    public function canBeCreatedFromString(): void
    {
        self::assertSame(ThemeMode::DARK, ThemeMode::from('dark'));
        self::assertNull(ThemeMode::tryFrom('nope'));
    }
}

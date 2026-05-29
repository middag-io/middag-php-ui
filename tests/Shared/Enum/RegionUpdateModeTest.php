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

use Middag\Ui\Shared\Enum\RegionUpdateMode;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversNothing]
final class RegionUpdateModeTest extends TestCase
{
    #[Test]
    public function allExpectedCasesExist(): void
    {
        $values = array_column(RegionUpdateMode::cases(), 'value');

        self::assertSame(['replace', 'append', 'prepend', 'remove', 'update'], $values);
    }

    #[Test]
    public function canBeCreatedFromString(): void
    {
        self::assertSame(RegionUpdateMode::REMOVE, RegionUpdateMode::from('remove'));
        self::assertNull(RegionUpdateMode::tryFrom('nope'));
    }
}

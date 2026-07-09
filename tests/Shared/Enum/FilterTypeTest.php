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

use Middag\Ui\Shared\Enum\FilterType;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversNothing]
final class FilterTypeTest extends TestCase
{
    #[Test]
    public function allExpectedCasesExist(): void
    {
        $values = array_column(FilterType::cases(), 'value');

        self::assertSame(
            ['select', 'text', 'date', 'date_range', 'boolean', 'number_range'],
            $values,
        );
    }

    #[Test]
    public function canBeCreatedFromString(): void
    {
        self::assertSame(FilterType::DateRange, FilterType::from('date_range'));
        self::assertNull(FilterType::tryFrom('nope'));
    }
}

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

use Middag\Ui\Shared\Enum\ValueFormat;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversNothing]
final class ValueFormatTest extends TestCase
{
    #[Test]
    public function allExpectedCasesExist(): void
    {
        $values = array_column(ValueFormat::cases(), 'value');

        self::assertSame(
            ['text', 'date', 'datetime', 'time', 'number', 'currency', 'boolean', 'badge'],
            $values,
        );
    }

    #[Test]
    public function canBeCreatedFromString(): void
    {
        self::assertSame(ValueFormat::CURRENCY, ValueFormat::from('currency'));
        self::assertNull(ValueFormat::tryFrom('nope'));
    }
}

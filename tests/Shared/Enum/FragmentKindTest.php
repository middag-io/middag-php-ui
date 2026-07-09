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

use Middag\Ui\Shared\Enum\FragmentKind;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversNothing]
final class FragmentKindTest extends TestCase
{
    #[Test]
    public function allExpectedCasesExist(): void
    {
        $values = array_column(FragmentKind::cases(), 'value');

        self::assertSame(
            ['block', 'region', 'table', 'form', 'detail', 'inspector', 'notifications', 'custom'],
            $values,
        );
    }

    #[Test]
    public function canBeCreatedFromString(): void
    {
        self::assertSame(FragmentKind::Custom, FragmentKind::from('custom'));
        self::assertNull(FragmentKind::tryFrom('nope'));
    }
}

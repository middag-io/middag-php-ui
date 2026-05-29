<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Tests\Enum;

use Middag\Ui\Enum\ActionIntent;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversNothing]
final class ActionIntentTest extends TestCase
{
    #[Test]
    public function allExpectedCasesExist(): void
    {
        $values = array_column(ActionIntent::cases(), 'value');

        self::assertSame(
            ['primary', 'secondary', 'danger', 'success', 'warning', 'info', 'link', 'ghost'],
            $values,
        );
    }

    #[Test]
    public function canBeCreatedFromString(): void
    {
        self::assertSame(ActionIntent::DANGER, ActionIntent::from('danger'));
        self::assertNull(ActionIntent::tryFrom('nope'));
    }
}

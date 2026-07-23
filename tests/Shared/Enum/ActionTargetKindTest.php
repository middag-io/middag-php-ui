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

use Middag\Ui\Shared\Enum\ActionTargetKind;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversNothing]
final class ActionTargetKindTest extends TestCase
{
    #[Test]
    public function allExpectedCasesExist(): void
    {
        $values = array_column(ActionTargetKind::cases(), 'value');

        self::assertSame(['link', 'route', 'request', 'panel'], $values);
    }

    #[Test]
    public function canBeCreatedFromString(): void
    {
        self::assertSame(ActionTargetKind::Request, ActionTargetKind::from('request'));
        self::assertNull(ActionTargetKind::tryFrom('nope'));
    }
}

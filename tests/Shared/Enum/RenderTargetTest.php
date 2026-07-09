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

use Middag\Ui\Shared\Enum\RenderTarget;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversNothing]
final class RenderTargetTest extends TestCase
{
    #[Test]
    public function allExpectedCasesExist(): void
    {
        $values = array_column(RenderTarget::cases(), 'value');

        self::assertSame(['html', 'props'], $values);
    }

    #[Test]
    public function canBeCreatedFromString(): void
    {
        self::assertSame(RenderTarget::Html, RenderTarget::from('html'));
        self::assertSame(RenderTarget::Props, RenderTarget::from('props'));
        self::assertNull(RenderTarget::tryFrom('nope'));
    }
}

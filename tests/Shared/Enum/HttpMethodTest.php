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

use Middag\Ui\Shared\Enum\HttpMethod;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversNothing]
final class HttpMethodTest extends TestCase
{
    #[Test]
    public function allExpectedCasesExist(): void
    {
        $values = array_column(HttpMethod::cases(), 'value');

        self::assertSame(['get', 'post', 'put', 'patch', 'delete'], $values);
    }

    #[Test]
    public function canBeCreatedFromString(): void
    {
        self::assertSame(HttpMethod::Delete, HttpMethod::from('delete'));
        self::assertNull(HttpMethod::tryFrom('nope'));
    }
}

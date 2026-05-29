<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Tests\Data;

use Middag\Ui\Data\Pagination;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @internal
 */
#[CoversClass(Pagination::class)]
final class PaginationTest extends TestCase
{
    #[Test]
    public function testIsReadonlyClass(): void
    {
        self::assertTrue((new ReflectionClass(Pagination::class))->isReadOnly());
    }

    #[Test]
    public function testSerializes(): void
    {
        $payload = (new Pagination(page: 2, perPage: 25, total: 100, lastPage: 4))->jsonSerialize();

        self::assertSame([
            'page' => 2,
            'perPage' => 25,
            'total' => 100,
            'lastPage' => 4,
        ], $payload);
    }

    #[Test]
    public function testOfDerivesLastPage(): void
    {
        self::assertSame(4, Pagination::of(1, 25, 100)->lastPage);
        self::assertSame(5, Pagination::of(1, 25, 101)->lastPage);
    }

    #[Test]
    public function testOfClampsLastPageToAtLeastOne(): void
    {
        self::assertSame(1, Pagination::of(1, 25, 0)->lastPage);
    }

    #[Test]
    public function testOfHandlesZeroPerPage(): void
    {
        self::assertSame(1, Pagination::of(1, 0, 100)->lastPage);
    }
}

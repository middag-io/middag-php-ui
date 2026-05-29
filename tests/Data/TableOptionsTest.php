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

use Middag\Ui\Data\TableOptions;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @internal
 */
#[CoversClass(TableOptions::class)]
final class TableOptionsTest extends TestCase
{
    #[Test]
    public function testIsReadonlyClass(): void
    {
        self::assertTrue((new ReflectionClass(TableOptions::class))->isReadOnly());
    }

    #[Test]
    public function testSerializesDefaults(): void
    {
        $payload = (new TableOptions())->jsonSerialize();

        self::assertSame([
            'perPage' => 25,
            'sortDirection' => 'desc',
            'selectable' => false,
            'searchable' => false,
        ], $payload);
        self::assertArrayNotHasKey('sortColumn', $payload);
    }

    #[Test]
    public function testSerializesSortColumnWhenSet(): void
    {
        $payload = (new TableOptions(
            perPage: 50,
            sortColumn: 'name',
            sortDirection: 'asc',
            selectable: true,
            searchable: true,
        ))->jsonSerialize();

        self::assertSame(50, $payload['perPage']);
        self::assertSame('name', $payload['sortColumn']);
        self::assertSame('asc', $payload['sortDirection']);
        self::assertTrue($payload['selectable']);
        self::assertTrue($payload['searchable']);
    }
}

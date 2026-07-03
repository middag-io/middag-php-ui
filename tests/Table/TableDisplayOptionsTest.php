<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Tests\Table;

use Middag\Ui\Table\TableDisplayOptions;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @internal
 */
#[CoversClass(TableDisplayOptions::class)]
final class TableDisplayOptionsTest extends TestCase
{
    #[Test]
    public function testIsReadonlyClass(): void
    {
        self::assertTrue((new ReflectionClass(TableDisplayOptions::class))->isReadOnly());
    }

    #[Test]
    public function testSerializesDefaults(): void
    {
        $payload = (new TableDisplayOptions())->jsonSerialize();

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
        $payload = (new TableDisplayOptions(
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

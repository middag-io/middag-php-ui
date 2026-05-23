<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Tests\Data\Table;

use Middag\Ui\Data\Table\Column;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @internal
 */
#[CoversClass(Column::class)]
final class ColumnTest extends TestCase
{
    #[Test]
    public function testIsReadonlyClass(): void
    {
        $ref = new ReflectionClass(Column::class);

        self::assertTrue($ref->isReadOnly());
    }

    #[Test]
    public function testDefaultValues(): void
    {
        $col = new Column(key: 'name', label: 'Name');

        self::assertSame('name', $col->key);
        self::assertSame('Name', $col->label);
        self::assertFalse($col->sortable);
        self::assertFalse($col->searchable);
        self::assertSame('text', $col->type);
        self::assertSame([], $col->options);
    }

    #[Test]
    public function testJsonSerializeMinimal(): void
    {
        $col = new Column(key: 'name', label: 'Name');

        self::assertSame([
            'key' => 'name',
            'label' => 'Name',
            'sortable' => false,
            'searchable' => false,
            'type' => 'text',
            'options' => [],
        ], $col->jsonSerialize());
    }

    #[Test]
    public function testJsonSerializeWithAllFields(): void
    {
        $col = new Column(
            key: 'status',
            label: 'Status',
            sortable: true,
            searchable: true,
            type: 'select',
            options: ['active' => 'Active', 'inactive' => 'Inactive'],
        );

        $payload = $col->jsonSerialize();

        self::assertSame('status', $payload['key']);
        self::assertSame('Status', $payload['label']);
        self::assertTrue($payload['sortable']);
        self::assertTrue($payload['searchable']);
        self::assertSame('select', $payload['type']);
        self::assertSame(['active' => 'Active', 'inactive' => 'Inactive'], $payload['options']);
    }

    #[Test]
    public function testJsonEncodeProducesValidJson(): void
    {
        $col = new Column(key: 'amount', label: 'Amount', sortable: true, type: 'number');

        $json = json_encode($col, JSON_THROW_ON_ERROR);

        self::assertJson($json);

        $decoded = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        self::assertSame('amount', $decoded['key']);
        self::assertSame('Amount', $decoded['label']);
        self::assertTrue($decoded['sortable']);
    }
}

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

use Middag\Ui\Shared\Data\Translatable;
use Middag\Ui\Shared\Enum\ValueFormat;
use Middag\Ui\Table\Column;
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
        self::assertSame(ValueFormat::TEXT, $col->format);
        self::assertSame([], $col->formatOptions);
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
            'format' => 'text',
        ], $col->jsonSerialize());
    }

    #[Test]
    public function testJsonSerializeWithAllFields(): void
    {
        $col = new Column(
            key: 'amount',
            label: 'Amount',
            sortable: true,
            searchable: true,
            format: ValueFormat::CURRENCY,
            formatOptions: ['currency' => 'BRL', 'decimals' => 2],
            options: ['align' => 'right'],
        );

        $payload = $col->jsonSerialize();

        self::assertSame('amount', $payload['key']);
        self::assertSame('Amount', $payload['label']);
        self::assertTrue($payload['sortable']);
        self::assertTrue($payload['searchable']);
        self::assertSame('currency', $payload['format']);
        self::assertSame(['currency' => 'BRL', 'decimals' => 2], $payload['formatOptions']);
        self::assertSame(['align' => 'right'], $payload['options']);
    }

    #[Test]
    public function testTranslatableLabelSerializesToIntent(): void
    {
        $col = new Column(key: 'name', label: Translatable::of('col_name', 'local_x'));

        $payload = $col->jsonSerialize();

        self::assertSame(['key' => 'col_name', 'domain' => 'local_x'], $payload['label']);
    }

    #[Test]
    public function testJsonEncodeProducesValidJson(): void
    {
        $col = new Column(key: 'amount', label: 'Amount', sortable: true, format: ValueFormat::NUMBER);

        $json = json_encode($col, JSON_THROW_ON_ERROR);

        self::assertJson($json);

        $decoded = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        self::assertSame('amount', $decoded['key']);
        self::assertSame('Amount', $decoded['label']);
        self::assertTrue($decoded['sortable']);
        self::assertSame('number', $decoded['format']);
    }
}

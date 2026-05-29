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

use Middag\Ui\Data\BulkAction;
use Middag\Ui\Data\Column;
use Middag\Ui\Data\FilterDefinition;
use Middag\Ui\Data\PageAction;
use Middag\Ui\Data\TableConfig;
use Middag\Ui\Data\TableOptions;
use Middag\Ui\Enum\FilterType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @internal
 */
#[CoversClass(TableConfig::class)]
final class TableConfigTest extends TestCase
{
    #[Test]
    public function testIsReadonlyClass(): void
    {
        $ref = new ReflectionClass(TableConfig::class);

        self::assertTrue($ref->isReadOnly());
    }

    #[Test]
    public function testDefaultValues(): void
    {
        $config = new TableConfig(columns: []);

        self::assertSame([], $config->columns);
        self::assertSame([], $config->filters);
        self::assertSame([], $config->rowActions);
        self::assertSame([], $config->bulkActions);
        self::assertInstanceOf(TableOptions::class, $config->options);
    }

    #[Test]
    public function testJsonSerializeKeysPresent(): void
    {
        $config = new TableConfig(columns: []);

        $payload = $config->jsonSerialize();

        self::assertArrayHasKey('columns', $payload);
        self::assertArrayHasKey('filters', $payload);
        self::assertArrayHasKey('rowActions', $payload);
        self::assertArrayHasKey('bulkActions', $payload);
        self::assertArrayHasKey('options', $payload);
    }

    #[Test]
    public function testColumnsSerializedViaJsonSerializable(): void
    {
        $col = new Column(key: 'name', label: 'Name', sortable: true);
        $config = new TableConfig(columns: [$col]);

        $json = json_encode($config, JSON_THROW_ON_ERROR);
        $decoded = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        self::assertCount(1, $decoded['columns']);
        self::assertSame('name', $decoded['columns'][0]['key']);
        self::assertSame('Name', $decoded['columns'][0]['label']);
        self::assertTrue($decoded['columns'][0]['sortable']);
    }

    #[Test]
    public function testFiltersRowActionsBulkActionsAndOptions(): void
    {
        $filter = new FilterDefinition(key: 'status', label: 'Status', type: FilterType::SELECT);
        $rowAction = new PageAction(id: 'edit', label: 'Edit', intent: 'secondary', href: '/x/{id}');
        $bulkAction = new BulkAction(id: 'delete', label: 'Delete', intent: 'danger', endpoint: '/x/bulk-delete');
        $options = new TableOptions(perPage: 50, selectable: true);

        $config = new TableConfig(
            columns: [],
            filters: [$filter],
            rowActions: [$rowAction],
            bulkActions: [$bulkAction],
            options: $options,
        );

        $payload = $config->jsonSerialize();

        self::assertSame([$filter->jsonSerialize()], $payload['filters']);
        self::assertSame([$rowAction->jsonSerialize()], $payload['rowActions']);
        self::assertSame([$bulkAction->jsonSerialize()], $payload['bulkActions']);
        self::assertSame($options->jsonSerialize(), $payload['options']);
        self::assertSame(50, $payload['options']['perPage']);
    }
}

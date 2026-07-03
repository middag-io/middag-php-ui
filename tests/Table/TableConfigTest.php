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

use Middag\Ui\Action\Action;
use Middag\Ui\Action\ActionTarget;
use Middag\Ui\Shared\Enum\ActionIntent;
use Middag\Ui\Shared\Enum\FilterType;
use Middag\Ui\Table\Column;
use Middag\Ui\Table\FilterDefinition;
use Middag\Ui\Table\TableConfig;
use Middag\Ui\Table\TableDisplayOptions;
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
        self::assertInstanceOf(TableDisplayOptions::class, $config->options);
    }

    #[Test]
    public function testJsonSerializeOmitsEmptyCollections(): void
    {
        $config = new TableConfig(columns: []);

        $payload = $config->jsonSerialize();

        // columns + options are structural and always present; the action/filter
        // collections follow the omit-empty convention like every other VO.
        self::assertArrayHasKey('columns', $payload);
        self::assertArrayHasKey('options', $payload);
        self::assertArrayNotHasKey('filters', $payload);
        self::assertArrayNotHasKey('rowActions', $payload);
        self::assertArrayNotHasKey('bulkActions', $payload);
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
        $rowAction = new Action(id: 'edit', label: 'Edit', target: ActionTarget::link('/x/{id}'));
        $bulkAction = new Action(id: 'delete', label: 'Delete', target: ActionTarget::request('/x/bulk-delete'), intent: ActionIntent::DANGER);
        $options = new TableDisplayOptions(perPage: 50, selectable: true);

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

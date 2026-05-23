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
use Middag\Ui\Data\Table\TableConfig;
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
        self::assertSame([], $config->actions);
        self::assertSame([], $config->options);
    }

    #[Test]
    public function testJsonSerializeKeysPresent(): void
    {
        $config = new TableConfig(columns: []);

        $payload = $config->jsonSerialize();

        self::assertArrayHasKey('columns', $payload);
        self::assertArrayHasKey('filters', $payload);
        self::assertArrayHasKey('actions', $payload);
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
    public function testFiltersAndActionsAndOptions(): void
    {
        $filter = ['key' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => []];
        $action = ['key' => 'delete', 'label' => 'Delete', 'icon' => 'trash', 'props' => []];
        $options = ['paginated' => true, 'perPage' => 25];

        $config = new TableConfig(
            columns: [],
            filters: [$filter],
            actions: [$action],
            options: $options,
        );

        $payload = $config->jsonSerialize();

        self::assertSame([$filter], $payload['filters']);
        self::assertSame([$action], $payload['actions']);
        self::assertSame($options, $payload['options']);
    }
}

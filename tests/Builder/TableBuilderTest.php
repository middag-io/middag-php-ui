<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Tests\Builder;

use Middag\Ui\Builder\TableBuilder;
use Middag\Ui\Data\Column;
use Middag\Ui\Data\TableConfig;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(TableBuilder::class)]
final class TableBuilderTest extends TestCase
{
    #[Test]
    public function testMakeReturnsBuilder(): void
    {
        self::assertInstanceOf(TableBuilder::class, TableBuilder::make());
    }

    #[Test]
    public function testBuildReturnsTableConfig(): void
    {
        $config = TableBuilder::make()->build();

        self::assertInstanceOf(TableConfig::class, $config);
    }

    #[Test]
    public function testEmptyBuildHasNoColumns(): void
    {
        $config = TableBuilder::make()->build();

        self::assertSame([], $config->columns);
    }

    #[Test]
    public function testAddColumn(): void
    {
        $config = TableBuilder::make()
            ->column('name', 'Name')
            ->build();

        self::assertCount(1, $config->columns);
        self::assertInstanceOf(Column::class, $config->columns[0]);
        self::assertSame('name', $config->columns[0]->key);
        self::assertSame('Name', $config->columns[0]->label);
    }

    #[Test]
    public function testColumnDefaults(): void
    {
        $config = TableBuilder::make()
            ->column('name', 'Name')
            ->build();

        $col = $config->columns[0];

        self::assertFalse($col->sortable);
        self::assertFalse($col->searchable);
        self::assertSame('text', $col->type);
        self::assertSame([], $col->options);
    }

    #[Test]
    public function testColumnWithSettings(): void
    {
        $config = TableBuilder::make()
            ->column('status', 'Status', [
                'sortable' => true,
                'searchable' => true,
                'type' => 'select',
                'options' => ['a' => 'Active'],
            ])
            ->build();

        $col = $config->columns[0];

        self::assertTrue($col->sortable);
        self::assertTrue($col->searchable);
        self::assertSame('select', $col->type);
        self::assertSame(['a' => 'Active'], $col->options);
    }

    #[Test]
    public function testAddFilter(): void
    {
        $config = TableBuilder::make()
            ->filter('status', 'Status', 'select', ['active' => 'Active'])
            ->build();

        self::assertCount(1, $config->filters);
        self::assertSame('status', $config->filters[0]['key']);
        self::assertSame('Status', $config->filters[0]['label']);
        self::assertSame('select', $config->filters[0]['type']);
        self::assertSame(['active' => 'Active'], $config->filters[0]['options']);
    }

    #[Test]
    public function testAddAction(): void
    {
        $config = TableBuilder::make()
            ->action('delete', 'Delete', 'trash')
            ->build();

        self::assertCount(1, $config->actions);
        self::assertSame('delete', $config->actions[0]['key']);
        self::assertSame('Delete', $config->actions[0]['label']);
        self::assertSame('trash', $config->actions[0]['icon']);
    }

    #[Test]
    public function testWithOptions(): void
    {
        $config = TableBuilder::make()
            ->withOptions(['paginated' => true, 'perPage' => 25])
            ->build();

        self::assertSame(['paginated' => true, 'perPage' => 25], $config->options);
    }

    #[Test]
    public function testWithOptionsMerges(): void
    {
        $config = TableBuilder::make()
            ->withOptions(['paginated' => true])
            ->withOptions(['perPage' => 50])
            ->build();

        self::assertTrue($config->options['paginated']);
        self::assertSame(50, $config->options['perPage']);
    }

    #[Test]
    public function testFluentChainReturnsBuilder(): void
    {
        $builder = TableBuilder::make();

        self::assertSame($builder, $builder->column('a', 'A'));
        self::assertSame($builder, $builder->filter('b', 'B'));
        self::assertSame($builder, $builder->action('c', 'C'));
        self::assertSame($builder, $builder->withOptions([]));
    }

    #[Test]
    public function testMultipleColumns(): void
    {
        $config = TableBuilder::make()
            ->column('name', 'Name')
            ->column('email', 'Email')
            ->column('status', 'Status')
            ->build();

        self::assertCount(3, $config->columns);
        self::assertSame('name', $config->columns[0]->key);
        self::assertSame('email', $config->columns[1]->key);
        self::assertSame('status', $config->columns[2]->key);
    }
}

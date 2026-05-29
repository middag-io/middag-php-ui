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
use Middag\Ui\Data\Action;
use Middag\Ui\Data\ActionTarget;
use Middag\Ui\Data\Column;
use Middag\Ui\Data\FilterDefinition;
use Middag\Ui\Data\TableConfig;
use Middag\Ui\Data\TableOptions;
use Middag\Ui\Enum\ActionIntent;
use Middag\Ui\Enum\FilterType;
use Middag\Ui\Enum\ValueFormat;
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
        self::assertSame(ValueFormat::TEXT, $col->format);
        self::assertSame([], $col->options);
    }

    #[Test]
    public function testColumnWithSettings(): void
    {
        $config = TableBuilder::make()
            ->column('amount', 'Amount', [
                'sortable' => true,
                'searchable' => true,
                'format' => ValueFormat::CURRENCY,
                'formatOptions' => ['currency' => 'BRL'],
                'options' => ['align' => 'right'],
            ])
            ->build();

        $col = $config->columns[0];

        self::assertTrue($col->sortable);
        self::assertTrue($col->searchable);
        self::assertSame(ValueFormat::CURRENCY, $col->format);
        self::assertSame(['currency' => 'BRL'], $col->formatOptions);
        self::assertSame(['align' => 'right'], $col->options);
    }

    #[Test]
    public function testAddFilter(): void
    {
        $config = TableBuilder::make()
            ->filter('status', 'Status', FilterType::SELECT, [
                ['value' => 'active', 'label' => 'Active'],
            ])
            ->build();

        self::assertCount(1, $config->filters);
        self::assertInstanceOf(FilterDefinition::class, $config->filters[0]);
        self::assertSame('status', $config->filters[0]->key);
        self::assertSame(FilterType::SELECT, $config->filters[0]->type);
    }

    #[Test]
    public function testAddRowAction(): void
    {
        $action = new Action(id: 'edit', label: 'Edit', target: ActionTarget::link('/x/{id}'));

        $config = TableBuilder::make()
            ->rowAction($action)
            ->build();

        self::assertCount(1, $config->rowActions);
        self::assertSame($action, $config->rowActions[0]);
    }

    #[Test]
    public function testAddBulkAction(): void
    {
        $action = new Action(id: 'delete', label: 'Delete', target: ActionTarget::request('/x/bulk-delete'), intent: ActionIntent::DANGER);

        $config = TableBuilder::make()
            ->bulkAction($action)
            ->build();

        self::assertCount(1, $config->bulkActions);
        self::assertSame($action, $config->bulkActions[0]);
    }

    #[Test]
    public function testOptions(): void
    {
        $options = new TableOptions(perPage: 50, selectable: true, searchable: true);

        $config = TableBuilder::make()
            ->options($options)
            ->build();

        self::assertSame($options, $config->options);
        self::assertSame(50, $config->options->perPage);
    }

    #[Test]
    public function testFluentChainReturnsBuilder(): void
    {
        $builder = TableBuilder::make();

        self::assertSame($builder, $builder->column('a', 'A'));
        self::assertSame($builder, $builder->filter('b', 'B'));
        self::assertSame($builder, $builder->rowAction(new Action(id: 'c', label: 'C', target: ActionTarget::link('/c'))));
        self::assertSame($builder, $builder->bulkAction(new Action(id: 'd', label: 'D', target: ActionTarget::request('/d'), intent: ActionIntent::DANGER)));
        self::assertSame($builder, $builder->options(new TableOptions()));
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

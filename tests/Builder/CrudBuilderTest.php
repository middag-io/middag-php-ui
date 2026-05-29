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

use InvalidArgumentException;
use Middag\Ui\Builder\CrudBuilder;
use Middag\Ui\PageContract;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(CrudBuilder::class)]
final class CrudBuilderTest extends TestCase
{
    #[Test]
    public function testDerivesSlugFromClass(): void
    {
        $crud = CrudBuilder::for('App\Entity\Invoice');

        self::assertSame('invoices', $crud->getSlug());
    }

    #[Test]
    public function testBuildIndexDefault(): void
    {
        $crud = CrudBuilder::for('App\Entity\Invoice');
        $contract = $crud->build('index');

        self::assertInstanceOf(PageContract::class, $contract);
        self::assertSame('invoices.index', $contract->page->key);
        self::assertSame('stack', $contract->layout->template);

        $blocks = $contract->layout->regions['content'] ?? [];
        self::assertCount(1, $blocks);
        self::assertSame('dense_table', $blocks[0]->jsonSerialize()['type']);
    }

    #[Test]
    public function testBuildIndexWithData(): void
    {
        $crud = CrudBuilder::for('App\Entity\Invoice');
        $rows = [['name' => 'INV-001']];
        $pagination = ['page' => 2, 'perPage' => 10, 'total' => 50, 'lastPage' => 5];

        $contract = $crud->build('index', ['rows' => $rows, 'pagination' => $pagination]);

        $block = $contract->layout->regions['content'][0];
        $data = $block->jsonSerialize()['data'];

        self::assertSame($rows, $data['rows']);
        self::assertSame($pagination, $data['pagination']);
    }

    #[Test]
    public function testWithoutRemovesAction(): void
    {
        $crud = CrudBuilder::for('App\Entity\Invoice');
        $crud->without('show');

        self::assertFalse($crud->hasAction('show'));
        self::assertTrue($crud->hasAction('index'));
    }

    #[Test]
    public function testCustomColumns(): void
    {
        $crud = CrudBuilder::for('App\Entity\Invoice');
        $crud->columns(['a', 'b']);

        $contract = $crud->build('index');

        $block = $contract->layout->regions['content'][0];
        $columns = $block->jsonSerialize()['data']['columns'];

        self::assertCount(2, $columns);
        self::assertSame('a', $columns[0]['key']);
        self::assertSame('b', $columns[1]['key']);
    }

    #[Test]
    public function testColumnConfigurator(): void
    {
        $crud = CrudBuilder::for('App\Entity\Invoice');
        $crud->columns(['status']);
        $crud->column('status', function (array &$c): void {
            $c['variant'] = 'badge';
        });

        $contract = $crud->build('index');

        $block = $contract->layout->regions['content'][0];
        $columns = $block->jsonSerialize()['data']['columns'];

        self::assertSame('badge', $columns[0]['variant']);
    }

    #[Test]
    public function testPerPage(): void
    {
        $crud = CrudBuilder::for('App\Entity\Invoice');
        $crud->perPage(50);

        $contract = $crud->build('index');

        $block = $contract->layout->regions['content'][0];
        $pagination = $block->jsonSerialize()['data']['pagination'];

        self::assertSame(50, $pagination['perPage']);
    }

    #[Test]
    public function testSort(): void
    {
        $crud = CrudBuilder::for('App\Entity\Invoice');
        $crud->sort('name', 'asc');

        $contract = $crud->build('index');

        $block = $contract->layout->regions['content'][0];
        $options = $block->jsonSerialize()['data']['options'];

        self::assertSame('name', $options['sortColumn']);
        self::assertSame('asc', $options['sortDirection']);
    }

    #[Test]
    public function testBuildCreate(): void
    {
        $crud = CrudBuilder::for('App\Entity\Invoice');
        $contract = $crud->build('create');

        self::assertInstanceOf(PageContract::class, $contract);
        self::assertSame('invoices.create', $contract->page->key);

        $block = $contract->layout->regions['content'][0];
        $data = $block->jsonSerialize()['data'];

        self::assertSame('form_panel', $block->jsonSerialize()['type']);
        self::assertSame('/invoices', $data['action']);
        self::assertSame('post', $data['method']);
    }

    #[Test]
    public function testBuildEdit(): void
    {
        $crud = CrudBuilder::for('App\Entity\Invoice');
        $contract = $crud->build('edit', ['id' => 42]);

        self::assertInstanceOf(PageContract::class, $contract);
        self::assertSame('invoices.edit', $contract->page->key);

        $block = $contract->layout->regions['content'][0];
        $data = $block->jsonSerialize()['data'];

        self::assertSame('form_panel', $block->jsonSerialize()['type']);
        self::assertSame('put', $data['method']);
        self::assertStringContainsString('42', $data['action']);
    }

    #[Test]
    public function testBuildShow(): void
    {
        $crud = CrudBuilder::for('App\Entity\Invoice');
        $contract = $crud->build('show');

        self::assertInstanceOf(PageContract::class, $contract);
        self::assertSame('invoices.show', $contract->page->key);
        self::assertSame('split', $contract->layout->template);

        $content = $contract->layout->regions['content'] ?? [];
        $aside = $contract->layout->regions['aside'] ?? [];

        self::assertCount(1, $content);
        self::assertSame('detail_panel', $content[0]->jsonSerialize()['type']);

        self::assertCount(1, $aside);
        self::assertSame('activity_timeline', $aside[0]->jsonSerialize()['type']);
    }

    #[Test]
    public function testInvalidActionThrows(): void
    {
        $crud = CrudBuilder::for('App\Entity\Invoice');

        $this->expectException(InvalidArgumentException::class);

        $crud->build('invalid');
    }

    #[Test]
    public function testOverrideSettersAreFluentAndBuildable(): void
    {
        $crud = CrudBuilder::for('App\Entity\Invoice');

        $result = $crud
            ->rowActions(['edit'])
            ->bulkActions(['delete'])
            ->pageActions([])
            ->form('App\Forms\InvoiceForm')
            ->title('Faturas')
            ->layout('custom-shell')
            ->capability('app/invoice:manage');

        self::assertSame($crud, $result);
        self::assertInstanceOf(PageContract::class, $crud->build('index'));
    }

    #[Test]
    public function testCustomTitleAppearsInContract(): void
    {
        $crud = CrudBuilder::for('App\Entity\Invoice')->title('Faturas');

        $payload = $crud->build('index')->jsonSerialize();

        self::assertStringContainsString('Faturas', (string) json_encode($payload));
    }

    #[Test]
    public function testCustomTitlePrefixesCreateAndEditTitles(): void
    {
        $crud = CrudBuilder::for('App\Entity\Invoice')->title('Fatura');

        $create = (string) json_encode($crud->build('create')->jsonSerialize());
        $edit = (string) json_encode($crud->build('edit', ['id' => 1])->jsonSerialize());

        self::assertStringContainsString('Create Fatura', $create);
        self::assertStringContainsString('Edit Fatura', $edit);
    }
}

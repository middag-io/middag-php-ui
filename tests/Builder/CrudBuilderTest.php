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
use Middag\Ui\Data\Translatable;
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
    public function testDerivesSingularSlugByDefault(): void
    {
        // The basename is a singular noun; the library never fabricates a plural.
        self::assertSame('invoice', CrudBuilder::for('App\Entity\Invoice')->getSlug());
    }

    #[Test]
    public function testExplicitSlugOverride(): void
    {
        self::assertSame('invoices', CrudBuilder::for('App\Entity\Invoice', 'invoices')->getSlug());
    }

    #[Test]
    public function testBuildIndexDefault(): void
    {
        $crud = CrudBuilder::for('App\Entity\Invoice');
        $contract = $crud->build('index');

        self::assertInstanceOf(PageContract::class, $contract);
        self::assertSame('invoice.index', $contract->page->key);
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
            $c['options'] = ['variant' => 'badge'];
        });

        $contract = $crud->build('index');

        $block = $contract->layout->regions['content'][0];
        $columns = $block->jsonSerialize()['data']['columns'];

        self::assertSame(['variant' => 'badge'], $columns[0]['options']);
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
        self::assertSame('invoice.create', $contract->page->key);

        $block = $contract->layout->regions['content'][0];
        $data = $block->jsonSerialize()['data'];

        self::assertSame('form_panel', $block->jsonSerialize()['type']);
        self::assertSame('/invoice', $data['action']);
        self::assertSame('post', $data['method']);
    }

    #[Test]
    public function testBuildEdit(): void
    {
        $crud = CrudBuilder::for('App\Entity\Invoice');
        $contract = $crud->build('edit', ['id' => 42]);

        self::assertInstanceOf(PageContract::class, $contract);
        self::assertSame('invoice.edit', $contract->page->key);

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
        self::assertSame('invoice.show', $contract->page->key);
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
            ->label('Fatura', 'Faturas')
            ->layout('custom-shell')
            ->capability('app/invoice:manage');

        self::assertSame($crud, $result);
        self::assertInstanceOf(PageContract::class, $crud->build('index'));
    }

    #[Test]
    public function testDefaultTitlesAreSingularNounWithoutVerb(): void
    {
        // No i18n, no label: literal singular noun for every action; no English verb.
        $crud = CrudBuilder::for('App\Entity\Invoice');

        self::assertSame('Invoice', $crud->build('index')->page->title);
        self::assertSame('Invoice', $crud->build('show')->page->title);
        self::assertSame('Invoice', $crud->build('create')->page->title);
        self::assertSame('Invoice', $crud->build('edit', ['id' => 1])->page->title);
    }

    #[Test]
    public function testSingularTitleDoesNotMangleSEndingNouns(): void
    {
        // 'Status' previously became 'Statu' (rtrim) and slug 'statuss' (naive +s).
        $crud = CrudBuilder::for('App\Entity\Status');

        self::assertSame('status', $crud->getSlug());
        self::assertSame('Status', $crud->build('create')->page->title);
        self::assertSame('Status', $crud->build('show')->page->title);
    }

    #[Test]
    public function testExplicitLabelDrivesSingularAndPlural(): void
    {
        $crud = CrudBuilder::for('App\Entity\Invoice')->label('Invoice', 'Invoices');

        self::assertSame('Invoices', $crud->build('index')->page->title);
        self::assertSame('Invoice', $crud->build('show')->page->title);
        self::assertSame('Invoice', $crud->build('create')->page->title);
    }

    #[Test]
    public function testI18nEmitsEntityNounIntents(): void
    {
        $crud = CrudBuilder::for('App\Entity\Invoice')->i18n('local_x');

        $show = $crud->build('show')->page->title;
        self::assertInstanceOf(Translatable::class, $show);
        self::assertSame('invoice', $show->key);
        self::assertSame('local_x', $show->domain);

        $index = $crud->build('index')->page->title;
        self::assertInstanceOf(Translatable::class, $index);
        self::assertSame('invoice_plural', $index->key);
        self::assertSame('local_x', $index->domain);
    }

    #[Test]
    public function testI18nCreateEditEmitVerbIntentInVerbDomain(): void
    {
        $crud = CrudBuilder::for('App\Entity\Invoice')->i18n('local_x');

        $create = $crud->build('create')->page->title;
        self::assertInstanceOf(Translatable::class, $create);
        self::assertSame('crud_create', $create->key);
        self::assertSame('ui', $create->domain);

        $entity = $create->params['entity'];
        self::assertInstanceOf(Translatable::class, $entity);
        self::assertSame('invoice', $entity->key);
        self::assertSame('local_x', $entity->domain);

        $edit = $crud->build('edit', ['id' => 1])->page->title;
        self::assertInstanceOf(Translatable::class, $edit);
        self::assertSame('crud_edit', $edit->key);
    }

    #[Test]
    public function testVerbDomainIsOverridable(): void
    {
        $create = CrudBuilder::for('App\Entity\Invoice')->i18n('local_x', verbs: 'core')->build('create')->page->title;

        self::assertInstanceOf(Translatable::class, $create);
        self::assertSame('core', $create->domain);
    }

    #[Test]
    public function testTranslatableLabelDerivesPluralAndVerb(): void
    {
        $crud = CrudBuilder::for('App\Entity\Invoice')->label(Translatable::of('inv', 'dom'));

        $index = $crud->build('index')->page->title;
        self::assertInstanceOf(Translatable::class, $index);
        self::assertSame('inv_plural', $index->key);
        self::assertSame('dom', $index->domain);

        $create = $crud->build('create')->page->title;
        self::assertInstanceOf(Translatable::class, $create);
        self::assertSame('crud_create', $create->key);
        self::assertSame('inv', $create->params['entity']->key);
    }

    #[Test]
    public function testRowActionsEmitTypedActionTargets(): void
    {
        $crud = CrudBuilder::for('App\Entity\Invoice')->rowActions(['show', 'edit', 'delete']);

        $data = $crud->build('index')->layout->regions['content'][0]->jsonSerialize()['data'];
        [$show, $edit, $delete] = $data['rowActions'];

        // show -> link to entity
        self::assertSame('link', $show['target']['kind']);
        self::assertSame('/invoice/{id}', $show['target']['href']);

        // edit -> link to /{slug}/{id}/edit (default branch)
        self::assertSame('link', $edit['target']['kind']);
        self::assertSame('/invoice/{id}/edit', $edit['target']['href']);

        // delete -> DELETE request, danger, with confirmation
        self::assertSame('request', $delete['target']['kind']);
        self::assertSame('delete', $delete['target']['method']);
        self::assertSame('danger', $delete['intent']);
        self::assertArrayHasKey('confirmation', $delete);
    }

    #[Test]
    public function testBulkActionsEmitTypedActionTargets(): void
    {
        $crud = CrudBuilder::for('App\Entity\Invoice')->bulkActions(['archive', 'delete']);

        $data = $crud->build('index')->layout->regions['content'][0]->jsonSerialize()['data'];
        [$archive, $delete] = $data['bulkActions'];

        // non-delete bulk -> POST request, secondary, no confirmation
        self::assertSame('request', $archive['target']['kind']);
        self::assertSame('post', $archive['target']['method']);
        self::assertSame('/invoice/bulk/archive', $archive['target']['endpoint']);
        self::assertSame('secondary', $archive['intent']);
        self::assertArrayNotHasKey('confirmation', $archive);

        // delete bulk -> danger + confirmation
        self::assertSame('danger', $delete['intent']);
        self::assertArrayHasKey('confirmation', $delete);
    }
}

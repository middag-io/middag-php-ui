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

use InvalidArgumentException;
use Middag\Ui\Action\Action;
use Middag\Ui\Action\ActionTarget;
use Middag\Ui\EditablePanel\EditablePanelDescriptor;
use Middag\Ui\Page\PageBuilder;
use Middag\Ui\Page\PageContract;
use Middag\Ui\Region\RegionBuilder;
use Middag\Ui\Shared\Enum\ColumnVariant;
use Middag\Ui\Shared\Enum\HttpMethod;
use Middag\Ui\Table\CrudBuilder;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Row action targets, column variants and page-level composition on top of
 * the CRUD convention.
 *
 * @internal
 */
#[CoversClass(CrudBuilder::class)]
final class CrudBuilderCompositionTest extends TestCase
{
    // ── Row actions ──────────────────────────────────────────────────────────

    #[Test]
    public function testRowActionInstanceKeepsItsPanelTarget(): void
    {
        $edit = new Action(id: 'edit', label: 'Edit', target: ActionTarget::panel(), icon: 'pencil');

        $data = $this->tableData(CrudBuilder::for('App\Agent')->rowActions([$edit])->build('index'));

        self::assertSame([$edit->jsonSerialize()], $data['rowActions']);
        self::assertSame(['kind' => 'panel'], $data['rowActions'][0]['target']);
    }

    #[Test]
    public function testRowActionsMixConventionNamesAndInstances(): void
    {
        $edit = new Action(id: 'edit', label: 'Edit', target: ActionTarget::panel());

        $data = $this->tableData(CrudBuilder::for('App\Agent', 'agents')->rowActions([$edit, 'delete'])->build('index'));

        self::assertSame(['edit', 'delete'], array_column($data['rowActions'], 'id'));
        self::assertSame('panel', $data['rowActions'][0]['target']['kind']);
        self::assertSame('request', $data['rowActions'][1]['target']['kind']);
        self::assertSame('/agents/{id}', $data['rowActions'][1]['target']['endpoint']);
    }

    #[Test]
    public function testCapabilityIsNotStampedOnCallerSuppliedRowAction(): void
    {
        $edit = new Action(id: 'edit', label: 'Edit', target: ActionTarget::panel());

        $data = $this->tableData(CrudBuilder::for('App\Agent')->capability('agents:edit')->rowActions([$edit, 'delete'])->build('index'));

        self::assertArrayNotHasKey('capability', $data['rowActions'][0]);
        self::assertSame('agents:edit', $data['rowActions'][1]['capability']);
    }

    // ── Column variants ──────────────────────────────────────────────────────

    #[Test]
    public function testColumnVariantSurvivesBuild(): void
    {
        $crud = CrudBuilder::for('App\Agent')
            ->columns(['name', 'role', 'workload'])
            ->column('role', static function (array &$col): void {
                $col['variant'] = ColumnVariant::Badge;
            })
            ->column('workload', static function (array &$col): void {
                $col['variant'] = ColumnVariant::Progress;
            });

        $columns = array_column($this->tableData($crud->build('index'))['columns'], null, 'key');

        self::assertArrayNotHasKey('variant', $columns['name']);
        self::assertSame('badge', $columns['role']['variant']);
        self::assertSame('progress', $columns['workload']['variant']);
    }

    #[Test]
    public function testColumnVariantAcceptsItsWireValue(): void
    {
        $crud = CrudBuilder::for('App\Agent')
            ->columns(['availability'])
            ->column('availability', static function (array &$col): void {
                $col['variant'] = 'html';
            });

        self::assertSame('html', $this->tableData($crud->build('index'))['columns'][0]['variant']);
    }

    #[Test]
    public function testUnknownColumnVariantIsRejected(): void
    {
        $crud = CrudBuilder::for('App\Agent')
            ->columns(['intake'])
            ->column('intake', static function (array &$col): void {
                $col['variant'] = 'not_a_variant';
            });

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('not_a_variant');

        $crud->build('index');
    }

    // ── Row href ─────────────────────────────────────────────────────────────

    #[Test]
    public function testRowHrefLandsInTheTableData(): void
    {
        $data = $this->tableData(CrudBuilder::for('App\Agent')->rowHref('/agents/{id}')->build('index'));

        self::assertSame('/agents/{id}', $data['rowHref']);
    }

    #[Test]
    public function testRowHrefIsOmittedByDefault(): void
    {
        self::assertArrayNotHasKey('rowHref', $this->tableData(CrudBuilder::for('App\Agent')->build('index')));
    }

    // ── Page configurator ────────────────────────────────────────────────────

    #[Test]
    public function testPageConfiguratorSetsShellSubtitleAndExtraRegion(): void
    {
        $contract = CrudBuilder::for('App\Agent', 'agents')
            ->page(static function (PageBuilder $page): void {
                $page->shell('basic')
                    ->subtitle('Who answers tickets')
                    ->region('metrics', static function (RegionBuilder $region): void {
                        $region->metricCard('agent_count', 3, 'Agents');
                    });
            })
            ->build('index');

        self::assertSame('basic', $contract->shell);
        self::assertSame('Who answers tickets', $contract->page->subtitle);
        self::assertSame(['content', 'metrics'], array_keys($contract->layout->regions));
        self::assertSame('agents.table', $contract->layout->regions['content'][0]->jsonSerialize()['key']);
        self::assertSame('metric_card', $contract->layout->regions['metrics'][0]->jsonSerialize()['type']);
    }

    #[Test]
    public function testPageConfiguratorReceivesTheAction(): void
    {
        $seen = [];
        $crud = CrudBuilder::for('App\Agent')->page(static function (PageBuilder $page, string $action) use (&$seen): void {
            $seen[] = $action;
        });

        foreach (['index', 'create', 'edit', 'show'] as $action) {
            $crud->build($action);
        }

        self::assertSame(['index', 'create', 'edit', 'show'], $seen);
    }

    #[Test]
    public function testPageConfiguratorCanOverrideConventionValues(): void
    {
        $contract = CrudBuilder::for('App\Agent')
            ->page(static function (PageBuilder $page): void {
                $page->title('Team')->layout('dashboard');
            })
            ->build('index');

        self::assertSame('Team', $contract->page->title);
        self::assertSame('dashboard', $contract->layout->template);
    }

    #[Test]
    public function testToPropsCarriesTheEditablePanel(): void
    {
        $props = CrudBuilder::for('App\Agent', 'agents')
            ->page(static function (PageBuilder $page): void {
                $page->editablePanel('/agents/{id}/panel', null, HttpMethod::Post, 380);
            })
            ->toProps('index', ['rows' => [['id' => 1]]]);

        self::assertInstanceOf(PageContract::class, $props['contract']);
        self::assertSame('agents.index', $props['contract']->page->key);
        self::assertInstanceOf(EditablePanelDescriptor::class, $props['editablePanel'] ?? null);
        self::assertSame(380, $props['editablePanel']->width);
    }

    #[Test]
    public function testToPropsWithoutConfiguratorHoldsOnlyTheContract(): void
    {
        $props = CrudBuilder::for('App\Agent')->toProps();

        self::assertSame(['contract'], array_keys($props));
        self::assertSame('agent.index', $props['contract']->page->key);
    }

    #[Test]
    public function testToPropsRejectsUnknownAction(): void
    {
        $this->expectException(InvalidArgumentException::class);

        CrudBuilder::for('App\Agent')->toProps('archive');
    }

    /**
     * @return array<string, mixed>
     */
    private function tableData(PageContract $contract): array
    {
        return $contract->layout->regions['content'][0]->jsonSerialize()['data'];
    }
}

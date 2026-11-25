<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Tests\Block;

use Middag\Ui\Action\Action;
use Middag\Ui\Action\ActionTarget;
use Middag\Ui\Action\ConditionalAction;
use Middag\Ui\Block\BlockBuilder;
use Middag\Ui\Block\CardGridBlockData;
use Middag\Ui\Block\CardGridColumnDef;
use Middag\Ui\Block\ChartSeries;
use Middag\Ui\Block\EmptyStateDef;
use Middag\Ui\Page\Tab;
use Middag\Ui\Shared\Enum\CardGridVariant;
use Middag\Ui\Shared\Enum\ChartType;
use Middag\Ui\Tests\Support\ValidatesAgainstSchema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * The schema-only block data shapes accept what the builders emit and reject
 * a payload that breaks their required keys or closed vocabularies.
 *
 * @internal
 */
#[CoversClass(CardGridBlockData::class)]
#[CoversClass(CardGridColumnDef::class)]
#[CoversClass(CardGridVariant::class)]
#[CoversClass(EmptyStateDef::class)]
#[CoversClass(ConditionalAction::class)]
final class BlockDataSchemaTest extends TestCase
{
    use ValidatesAgainstSchema;

    #[Test]
    public function cardGridAcceptsWhatTheBuilderEmits(): void
    {
        $block = BlockBuilder::cardGrid(
            'grid',
            [['key' => 'name', 'label' => 'Name'], ['key' => 'status', 'label' => 'Status', 'kind' => 'status']],
            [['id' => 1, 'name' => 'A', 'status' => 'active']],
            CardGridVariant::Connector->value,
            [
                'emptyState' => ['icon' => 'box', 'title' => 'Nothing yet', 'cta' => ['label' => 'Create', 'href' => '/new']],
                'editHref' => '/items/{id}/edit',
                'rowActions' => [new Action('delete', 'Delete', ActionTarget::link('/items/{id}/delete'))],
            ],
        );

        self::assertValidAgainst('CardGridBlockData', $block->jsonSerialize()['data']);
    }

    #[Test]
    public function cardGridRejectsMissingColumnsAndUnknownKeys(): void
    {
        self::assertInvalidAgainst('CardGridBlockData', ['rows' => []]);
        self::assertInvalidAgainst('CardGridBlockData', ['columns' => [], 'rows' => [], 'unknown' => true]);
        self::assertInvalidAgainst('CardGridColumnDef', ['key' => 'a', 'label' => 'A', 'kind' => 'chart']);
        self::assertInvalidAgainst('CardGridColumnDef', ['key' => 'a']);
    }

    #[Test]
    public function cardGridVariantListsTheKnownValues(): void
    {
        self::assertSame(['type' => 'string', 'enum' => ['default', 'store', 'connector']], CardGridVariant::jsonSchema());
    }

    #[Test]
    public function emptyStateDefNeedsLabelAndHrefOnItsCta(): void
    {
        self::assertValidAgainst('EmptyStateDef', ['description' => 'Nothing here']);
        self::assertInvalidAgainst('EmptyStateDef', ['cta' => ['label' => 'Go']]);
    }

    #[Test]
    public function conditionalActionExtendsActionWithRowConditions(): void
    {
        $action = (new Action('archive', 'Archive', ActionTarget::link('/archive/{id}')))->jsonSerialize();

        self::assertValidAgainst('ConditionalAction', $action);
        self::assertValidAgainst('ConditionalAction', [
            ...$action,
            'variant' => 'icon',
            'visible_when' => ['field' => 'status', 'operator' => 'equals', 'value' => 'active'],
            'disabled_when' => ['field' => 'locked', 'operator' => 'in', 'value' => ['1']],
            'loading_when' => ['field' => 'busy', 'operator' => 'equals', 'value' => '1'],
            'disabled_reason_field' => 'locked_reason',
        ]);
        self::assertInvalidAgainst('ConditionalAction', [...$action, 'variant' => 'menu']);
        self::assertInvalidAgainst('ConditionalAction', [...$action, 'extra' => true]);
    }

    #[Test]
    public function metricCardCarriesDeltaDirection(): void
    {
        $data = BlockBuilder::metricCard('m', 42, 'Open', '+3', deltaDirection: 'positive')->jsonSerialize()['data'];

        self::assertSame('positive', $data['deltaDirection']);
        self::assertValidAgainst('MetricCardBlockData', $data);
        self::assertArrayNotHasKey('deltaDirection', BlockBuilder::metricCard('m', 1, 'A')->jsonSerialize()['data']);
        self::assertInvalidAgainst('MetricCardBlockData', ['value' => 1, 'label' => 'A', 'deltaDirection' => 'up']);
    }

    #[Test]
    public function emptyStateCarriesCtaSecondary(): void
    {
        $data = BlockBuilder::emptyState('e', 'no-results', 'Nothing found', 'Reset', ctaHref: '/reset', ctaSecondary: ['label' => 'Docs', 'href' => '/docs'])->jsonSerialize()['data'];

        self::assertSame(['label' => 'Docs', 'href' => '/docs'], $data['ctaSecondary']);
        self::assertValidAgainst('EmptyStateBlockData', $data);
        self::assertArrayNotHasKey('ctaSecondary', BlockBuilder::emptyState('e', description: 'x')->jsonSerialize()['data']);
        self::assertInvalidAgainst('EmptyStateBlockData', ['description' => 'x', 'ctaSecondary' => ['label' => 'Docs']]);
    }

    #[Test]
    public function statusStripCarriesScore(): void
    {
        $items = [['key' => 'sla', 'label' => 'SLA', 'value' => '98%', 'appearance' => 'success']];
        $data = BlockBuilder::statusStrip('s', $items, 'success', 87)->jsonSerialize()['data'];

        self::assertSame(['items' => $items, 'tone' => 'success', 'score' => 87], $data);
        self::assertValidAgainst('StatusStripBlockData', $data);
        self::assertSame(['items' => $items], BlockBuilder::statusStrip('s', $items)->jsonSerialize()['data']);
        self::assertInvalidAgainst('StatusStripBlockData', ['items' => [['key' => 'a', 'label' => 'A', 'value' => 'v', 'appearance' => 'red']]]);
    }

    #[Test]
    public function activityTimelineCarriesLimit(): void
    {
        $entry = ['id' => '1', 'actor' => 'System', 'action' => 'created', 'icon' => 'plus', 'color' => 'info', 'timestamp' => 1700000000];
        $data = BlockBuilder::activityTimeline('a', [['label' => 'Today', 'entries' => [$entry]]], limit: 5)->jsonSerialize()['data'];

        self::assertSame(5, $data['limit']);
        self::assertValidAgainst('ActivityTimelineBlockData', $data);
        self::assertArrayNotHasKey('limit', BlockBuilder::activityTimeline('a', [])->jsonSerialize()['data']);
        self::assertInvalidAgainst('ActivityTimelineEntry', [...$entry, 'color' => 'pink']);
    }

    #[Test]
    public function builderOutputValidatesForTheOtherBlocks(): void
    {
        $action = (new Action('open', 'Open', ActionTarget::link('/open')))->jsonSerialize();

        self::assertValidAgainst('DenseTableBlockData', BlockBuilder::denseTable(
            't',
            [['key' => 'name', 'label' => 'Name', 'variant' => 'status', 'statusMap' => ['Ativo' => 'success']]],
            [['name' => 'A']],
            ['pagination' => ['page' => 1, 'perPage' => 10, 'total' => 1, 'lastPage' => 1], 'sort' => ['column' => null, 'direction' => null],
                'filters' => ['available' => [['key' => 's', 'label' => 'S', 'type' => 'select', 'options' => [['value' => 'a', 'label' => 'A']]]], 'applied' => ['s' => 'a']],
                'rowActions' => [$action], 'bulkActions' => [$action], 'saveViewAction' => $action],
        )->jsonSerialize()['data']);
        self::assertValidAgainst('DetailPanelBlockData', BlockBuilder::detailPanel('d', [
            ['id' => 'main', 'title' => 'Main', 'fields' => [['key' => 'id', 'label' => 'Id', 'value' => 7, 'entity' => ['type' => 'user', 'id' => 7]]], 'actions' => [$action]],
        ], ['columns' => 2])->jsonSerialize()['data']);
        self::assertValidAgainst('MarkdownPanelBlockData', BlockBuilder::markdownPanel('m', '# Hi', 300)->jsonSerialize()['data']);
        self::assertValidAgainst('LinkListBlockData', BlockBuilder::linkList('l', [['label' => 'A', 'href' => null, 'trailing' => '3', 'active' => true]])->jsonSerialize()['data']);
        self::assertValidAgainst('ChartBlockData', BlockBuilder::chart('c', ChartType::Bar, [new ChartSeries('v', 'V')], 'day', [['day' => 'Mon', 'v' => 1]], ['showLegend' => true])->jsonSerialize()['data']);
        self::assertValidAgainst('TabsBlockData', BlockBuilder::tabs('t', [new Tab('a', 'A', [])], 'a')->jsonSerialize()['data']);
        self::assertValidAgainst('ActionGridBlockData', BlockBuilder::actionGrid('g', [[...$action, 'title' => 'Open', 'description' => 'Open it']], ['success' => true, 'message' => 'Done'])->jsonSerialize()['data']);
    }

    #[Test]
    public function cellValueShapesAcceptTheirDocumentedForm(): void
    {
        self::assertValidAgainst('RichStatusValue', ['label' => 'Up', 'appearance' => 'success', 'icon' => 'check']);
        self::assertValidAgainst('AnnotatedValue', ['text' => 'Main', 'badge' => ['label' => 'M', 'color' => '#000'], 'sublabel' => 'x', 'appearance' => 'info']);
        self::assertValidAgainst('ProgressValue', ['value' => 3, 'max' => 10, 'label' => '30%']);
        self::assertValidAgainst('LinkGroupItem', ['icon' => 'link', 'href' => null, 'label' => 'Site']);
        self::assertValidAgainst('EntityRef', ['type' => 'user', 'id' => 'userId']);
        self::assertInvalidAgainst('RichStatusValue', ['label' => 'Up']);
        self::assertInvalidAgainst('DenseTableSort', ['column' => 'a', 'direction' => 'up']);
        self::assertInvalidAgainst('ActionGridItem', ['id' => 'a', 'label' => 'A', 'target' => ['kind' => 'link', 'href' => '/a'], 'intent' => 'primary']);
    }
}

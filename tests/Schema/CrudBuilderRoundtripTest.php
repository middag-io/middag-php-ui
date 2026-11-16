<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Tests\Schema;

use Middag\Ui\Action\Action;
use Middag\Ui\Action\ActionTarget;
use Middag\Ui\Page\PageBuilder;
use Middag\Ui\Page\PageContract;
use Middag\Ui\Region\RegionBuilder;
use Middag\Ui\Shared\Enum\ColumnVariant;
use Middag\Ui\Shared\Enum\HttpMethod;
use Middag\Ui\Table\CrudBuilder;
use Middag\Ui\Tests\Support\ValidatesAgainstSchema;
use Opis\JsonSchema\Errors\ErrorFormatter;
use Opis\JsonSchema\Errors\ValidationError;
use Opis\JsonSchema\Validator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Serializes pages built by {@see CrudBuilder} and validates them against the
 * published wire schema (`schema/page-contract.json`).
 *
 * A dense_table block carries free-form `data`, so the whole-page check alone
 * says nothing about the table: the emitted columns and row actions are also
 * validated one by one against their own `$defs`.
 *
 * @internal
 */
#[CoversClass(CrudBuilder::class)]
final class CrudBuilderRoundtripTest extends TestCase
{
    use ValidatesAgainstSchema;

    private const string PUBLISHED_SCHEMA = __DIR__ . '/../../schema/page-contract.json';

    #[Test]
    public function testPanelRowActionValidates(): void
    {
        $contract = CrudBuilder::for('App\Agent', 'agents')
            ->rowActions([new Action(id: 'edit', label: 'Edit', target: ActionTarget::panel()), 'delete'])
            ->build('index');

        $this->assertValidAgainstPublishedSchema($contract);

        $rowActions = $this->tableData($contract)['rowActions'];
        self::assertSame(['kind' => 'panel'], $rowActions[0]['target']);

        foreach ($rowActions as $action) {
            self::assertValidAgainst('Action', $action);
        }
    }

    #[Test]
    #[DataProvider('variantProvider')]
    public function testEachColumnVariantValidates(ColumnVariant $variant): void
    {
        $contract = CrudBuilder::for('App\Agent')
            ->columns(['value'])
            ->column('value', static function (array &$col) use ($variant): void {
                $col['variant'] = $variant;
            })
            ->build('index');

        $this->assertValidAgainstPublishedSchema($contract);

        $column = $this->tableData($contract)['columns'][0];
        self::assertSame($variant->value, $column['variant']);
        self::assertValidAgainst('Column', $column);
    }

    /**
     * @return array<string, array{ColumnVariant}>
     */
    public static function variantProvider(): array
    {
        $cases = [];

        foreach (ColumnVariant::cases() as $variant) {
            $cases[$variant->value] = [$variant];
        }

        return $cases;
    }

    #[Test]
    public function testMultiRegionIndexPageValidates(): void
    {
        $props = CrudBuilder::for('App\Agent', 'agents')
            ->columns(['name', 'role'])
            ->column('role', static function (array &$col): void {
                $col['variant'] = ColumnVariant::Badge;
            })
            ->rowActions([new Action(id: 'edit', label: 'Edit', target: ActionTarget::panel(), icon: 'pencil')])
            ->rowHref('/agents/{id}')
            ->page(static function (PageBuilder $page): void {
                $page->shell('basic')
                    ->layout('dashboard')
                    ->subtitle('Who answers tickets')
                    ->region('metrics', static function (RegionBuilder $region): void {
                        $region->metricCard('agent_count', 3, 'Agents', icon: 'users');
                    })
                    ->editablePanel('/agents/{id}/panel', null, HttpMethod::Post, 380);
            })
            ->toProps('index', ['rows' => [['id' => 1, 'name' => 'Ada', 'role' => 'agent']]]);

        $contract = $props['contract'];
        self::assertInstanceOf(PageContract::class, $contract);
        $this->assertValidAgainstPublishedSchema($contract);
        self::assertCount(2, $contract->layout->regions);
        self::assertValidAgainst('EditablePanelDescriptor', $props['editablePanel'] ?? null);
    }

    #[Test]
    public function testPublishedSchemaRejectsABrokenPage(): void
    {
        // Guards the checks above against a vacuous schema: a built page with
        // its required `version` removed must fail.
        $payload = CrudBuilder::for('App\Agent')->build('index')->jsonSerialize();
        unset($payload['version']);

        $result = (new Validator())->validate(self::asData($payload), $this->publishedSchema());

        self::assertFalse($result->isValid());
    }

    #[Test]
    #[DataProvider('actionProvider')]
    public function testEveryConventionPageValidates(string $action): void
    {
        $this->assertValidAgainstPublishedSchema(CrudBuilder::for('App\Agent')->build($action));
    }

    /**
     * @return array<string, array{string}>
     */
    public static function actionProvider(): array
    {
        return ['index' => ['index'], 'create' => ['create'], 'edit' => ['edit'], 'show' => ['show']];
    }

    private function assertValidAgainstPublishedSchema(PageContract $contract): void
    {
        $result = (new Validator())->validate(self::asData($contract), $this->publishedSchema());

        if (!$result->isValid()) {
            $error = $result->error();
            $formatted = $error instanceof ValidationError ? (new ErrorFormatter())->format($error) : [];
            self::fail('The built page does not validate against schema/page-contract.json:' . "\n"
                . json_encode($formatted, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }

        self::assertTrue($result->isValid());
    }

    private function publishedSchema(): mixed
    {
        return json_decode((string) file_get_contents(self::PUBLISHED_SCHEMA));
    }

    /**
     * @return array<string, mixed>
     */
    private function tableData(PageContract $contract): array
    {
        return $contract->layout->regions['content'][0]->jsonSerialize()['data'];
    }
}

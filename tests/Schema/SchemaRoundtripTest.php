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

use JsonSerializable;
use Middag\Ui\Action\Action;
use Middag\Ui\Action\ActionResult;
use Middag\Ui\Action\ActionTarget;
use Middag\Ui\Action\Confirmation;
use Middag\Ui\Block\BlockDescriptor;
use Middag\Ui\Block\ChartSeries;
use Middag\Ui\Block\LayoutDescriptor;
use Middag\Ui\Form\FieldConstraints;
use Middag\Ui\Form\FileValue;
use Middag\Ui\Form\FormErrors;
use Middag\Ui\Form\FormStep;
use Middag\Ui\Inspector\InspectorDescriptor;
use Middag\Ui\Navigation\Breadcrumb;
use Middag\Ui\Navigation\NavigationNode;
use Middag\Ui\Page\Branding;
use Middag\Ui\Page\PageContract;
use Middag\Ui\Page\PageMeta;
use Middag\Ui\Page\PageResources;
use Middag\Ui\Page\ResourcePatch;
use Middag\Ui\Page\Tab;
use Middag\Ui\Region\Fragment;
use Middag\Ui\Region\PollConfig;
use Middag\Ui\Region\RegionUpdate;
use Middag\Ui\Schema\SchemaRegistry;
use Middag\Ui\Shared\Enum\ActionIntent;
use Middag\Ui\Shared\Enum\HttpMethod;
use Middag\Ui\Shared\Enum\NotificationLevel;
use Middag\Ui\Shared\Enum\ValueFormat;
use Middag\Ui\Shared\ValueObject\Identity;
use Middag\Ui\Shared\ValueObject\Notification;
use Middag\Ui\Shared\ValueObject\Translatable;
use Middag\Ui\Shared\ValueObject\UserPreferences;
use Middag\Ui\Table\Column;
use Middag\Ui\Table\FilterDefinition;
use Middag\Ui\Table\Pagination;
use Middag\Ui\Table\TableConfig;
use Middag\Ui\Table\TableDisplayOptions;
use Opis\JsonSchema\Errors\ErrorFormatter;
use Opis\JsonSchema\Errors\ValidationError;
use Opis\JsonSchema\Validator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * Proves the hand-authored jsonSchema() of each VO accepts the real
 * jsonSerialize() output — so the schema (the codegen source of truth) can never
 * drift from the wire. Validates with opis/json-schema (draft 2020-12).
 *
 * @internal
 */
#[CoversClass(SchemaRegistry::class)]
final class SchemaRoundtripTest extends TestCase
{
    // ── ActionTarget — the discriminated union ───────────────────────────────

    #[Test]
    public function testActionTargetLink(): void
    {
        $this->assertValidAgainst('ActionTarget', ActionTarget::link('/x', external: true));
        $this->assertValidAgainst('ActionTarget', ActionTarget::link('/x'));
    }

    #[Test]
    public function testActionTargetRoute(): void
    {
        $this->assertValidAgainst('ActionTarget', ActionTarget::route('x.show', ['id' => 7]));
        $this->assertValidAgainst('ActionTarget', ActionTarget::route('x.index'));
    }

    #[Test]
    public function testActionTargetRequest(): void
    {
        $this->assertValidAgainst('ActionTarget', ActionTarget::request('/x/{id}', HttpMethod::DELETE));
    }

    // ── Action — minimal and maximal ─────────────────────────────────────────

    #[Test]
    public function testActionMinimal(): void
    {
        $this->assertValidAgainst('Action', new Action(id: 'a', label: 'A', target: ActionTarget::route('x.index')));
    }

    #[Test]
    public function testActionMaximal(): void
    {
        $this->assertValidAgainst('Action', new Action(
            id: 'del',
            label: Translatable::of('btn_delete', 'local_x', ['name' => 'Item']),
            target: ActionTarget::request('/x/{id}', HttpMethod::DELETE),
            intent: ActionIntent::DANGER,
            icon: 'trash',
            confirmation: new Confirmation(title: 'Delete?', message: Translatable::of('confirm', 'local_x'), variant: 'danger'),
            capability: 'manage_x',
            disabled: true,
            loading: true,
        ));
    }

    // ── Label union (both branches) ──────────────────────────────────────────

    #[Test]
    public function testBreadcrumbLabelBranches(): void
    {
        $this->assertValidAgainst('Breadcrumb', new Breadcrumb('Home', '/', external: true));
        $this->assertValidAgainst('Breadcrumb', new Breadcrumb(Translatable::of('home', 'core')));
    }

    // ── NavigationNode — bool-only-when-true + recursion ─────────────────────

    #[Test]
    public function testNavigationNodeFlagsAndChildren(): void
    {
        $child = new NavigationNode(key: 'leaf', label: 'Leaf', href: '/leaf', active: true);
        $node = new NavigationNode(
            key: 'group',
            label: Translatable::of('grp', 'core'),
            icon: 'folder',
            badge: '3',
            drilldown: true,
            collapsible: true,
            defaultOpen: true,
            children: [$child],
        );
        $this->assertValidAgainst('NavigationNode', $node);
    }

    // ── FieldConstraints — empty {} and full ─────────────────────────────────

    #[Test]
    public function testFieldConstraintsEmptyAndFull(): void
    {
        $this->assertValidAgainst('FieldConstraints', new FieldConstraints());
        $this->assertValidAgainst('FieldConstraints', new FieldConstraints(
            required: true,
            min: 1,
            max: 9,
            minLength: 2,
            maxLength: 8,
            pattern: '\d+',
            step: '0.5',
        ));
    }

    // ── FileValue — url shorthand + full metadata ────────────────────────────

    #[Test]
    public function testFileValue(): void
    {
        $this->assertValidAgainst('FileValue', new FileValue('/files/x.pdf'));
        $this->assertValidAgainst('FileValue', new FileValue(
            url: '/files/report.pdf',
            name: 'report.pdf',
            size: 10240,
            type: 'application/pdf',
            id: 'f-7',
            draftitemid: 99887766,
        ));
    }

    // ── FormErrors — field, list and form-level (_) keys ─────────────────────

    #[Test]
    public function testFormErrors(): void
    {
        // Empty errors are never serialized (ActionResult omits them), so the
        // schema only needs to accept the populated object form.
        $this->assertValidAgainst('FormErrors', new FormErrors([
            'email' => 'Required',
            'tags' => ['Too few', 'Invalid value'],
            'address.zip' => 'Bad ZIP',
            FormErrors::FORM_LEVEL_KEY => 'The form could not be saved.',
        ]));
    }

    // ── Notification + enum ──────────────────────────────────────────────────

    #[Test]
    public function testNotification(): void
    {
        $this->assertValidAgainst('Notification', new Notification(
            level: NotificationLevel::ERROR,
            message: Translatable::of('failed', 'core'),
            title: 'Oops',
            timeout: 5000,
            action: new Action(id: 'retry', label: 'Retry', target: ActionTarget::request('/retry')),
        ));
    }

    // ── Tables ───────────────────────────────────────────────────────────────

    #[Test]
    public function testTableConfig(): void
    {
        $table = new TableConfig(
            columns: [new Column(key: 'name', label: 'Name', sortable: true, format: ValueFormat::TEXT)],
            filters: [new FilterDefinition(key: 'status', label: 'Status')],
            rowActions: [new Action(id: 'edit', label: 'Edit', target: ActionTarget::link('/x/1/edit'))],
            options: new TableDisplayOptions(perPage: 50, sortColumn: 'name', selectable: true),
        );
        $this->assertValidAgainst('TableConfig', $table);
        $this->assertValidAgainst('Pagination', new Pagination(1, 25, 100, 4));
    }

    // ── Resources (capabilities non-empty + empty) ───────────────────────────

    #[Test]
    public function testPageResources(): void
    {
        $this->assertValidAgainst('PageResources', new PageResources(
            preferences: new UserPreferences(locale: 'pt-BR', timezone: 'America/Sao_Paulo'),
            capabilities: ['manage_x' => true],
            featureFlags: ['beta' => false],
            user: new Identity(id: '1', name: 'Ana', email: 'a@x.io', roles: ['admin']),
            branding: new Branding(appName: 'Helico', logoUrl: '/logo.svg'),
        ));
        // Empty capability/featureFlag maps serialize as [] — must still validate.
        $this->assertValidAgainst('PageResources', new PageResources());
    }

    // ── Server-push slices ───────────────────────────────────────────────────

    #[Test]
    public function testFragmentAndRegionUpdate(): void
    {
        $block = new BlockDescriptor(type: 'metric_card', key: 'm1', data: ['value' => 42, 'label' => 'Total']);
        $this->assertValidAgainst('Fragment', Fragment::block($block));
        $this->assertValidAgainst('Fragment', Fragment::custom('toast', new Pagination(1, 1, 1, 1)));
        $this->assertValidAgainst('RegionUpdate', RegionUpdate::replace('content', $block));
        $this->assertValidAgainst('RegionUpdate', RegionUpdate::remove('content', 'm1', 'm2'));
    }

    #[Test]
    public function testActionResult(): void
    {
        $this->assertValidAgainst('ActionResult', new ActionResult(
            success: false,
            notifications: [new Notification(level: NotificationLevel::WARNING, message: 'Heads up')],
            redirect: '/x',
            refreshBlocks: ['m1', 'm2'],
            errors: ['name' => 'required', 'tags' => ['too few', 'invalid']],
            resources: new ResourcePatch(capabilities: ['x' => true], featureFlags: ['beta' => true]),
        ));
    }

    // ── Misc leaf VOs ────────────────────────────────────────────────────────

    #[Test]
    public function testMiscLeaves(): void
    {
        $this->assertValidAgainst('ChartSeries', new ChartSeries('Revenue', [1.0, 2.5, 3.0]));
        $this->assertValidAgainst('PollConfig', new PollConfig(endpoint: '/poll', intervalMs: 5000, stopAfterMs: 60000));
        $this->assertValidAgainst('InspectorDescriptor', new InspectorDescriptor(endpoint: '/inspect/{id}', width: 480, poll: new PollConfig('/p', 1000)));
        $this->assertValidAgainst('FormStep', new FormStep(id: 's1', label: 'Step', fields: ['name', 'email'], help: Translatable::of('help', 'core')));
        $this->assertValidAgainst('Tab', new Tab(id: 't1', label: 'Tab', blocks: []));
    }

    // ── The whole tree: a realistic PageContract ─────────────────────────────

    #[Test]
    public function testFullPageContract(): void
    {
        $table = new BlockDescriptor(
            type: 'dense_table',
            key: 'courses',
            data: ['rows' => [['id' => 1, 'name' => 'Math']]],
            title: Translatable::of('courses', 'local_x'),
            actions: [
                new Action(id: 'new', label: 'New', target: ActionTarget::link('/courses/new')),
                new Action(id: 'sync', label: Translatable::of('sync', 'local_x'), target: ActionTarget::request('/sync', HttpMethod::POST), intent: ActionIntent::PRIMARY),
            ],
            poll: new PollConfig(endpoint: '/courses/poll', intervalMs: 10000),
        );

        $page = new PageContract(
            shell: 'product',
            page: new PageMeta(
                key: 'courses.index',
                title: Translatable::of('courses_title', 'local_x'),
                subtitle: 'Manage courses',
                breadcrumbs: [new Breadcrumb('Home', '/'), new Breadcrumb(Translatable::of('courses', 'local_x'))],
                actions: [new Action(id: 'help', label: 'Help', target: ActionTarget::route('help.show'))],
            ),
            layout: new LayoutDescriptor(template: 'stack', regions: ['content' => [$table]], meta: ['gap' => 4]),
            resources: new PageResources(capabilities: ['manage_courses' => true]),
            notifications: [new Notification(level: NotificationLevel::INFO, message: 'Loaded')],
            entities: ['course' => '/courses/{id}', 'user' => '/users/{id}'],
        );

        $this->assertValidAgainst('PageContract', $page);
    }

    // ── Empty-array serialization branches (the [] vs {} gotcha) ─────────────

    #[Test]
    public function testEmptyArraySerializationBranches(): void
    {
        // A data-less block (divider/spacer) serializes `data` to [] — must validate.
        $this->assertValidAgainst('BlockDescriptor', new BlockDescriptor(type: 'divider', key: 'd1', data: []));
        // A layout with no regions — or only empty ones (array_filter drops them) —
        // serializes `regions` to [].
        $this->assertValidAgainst('LayoutDescriptor', new LayoutDescriptor(template: 'blank', regions: []));
        $this->assertValidAgainst('LayoutDescriptor', new LayoutDescriptor(template: 'stack', regions: ['content' => []]));
        // An empty resource patch serializes to [].
        $this->assertValidAgainst('ResourcePatch', new ResourcePatch());
    }

    // ── Negative: required[] enforced (roundtrip alone is forward-only) ──────

    #[Test]
    public function testRequiredKeysAreEnforced(): void
    {
        // Action without `target` must FAIL — pins a required[] invariant an
        // always-populated happy-path roundtrip cannot catch (it would still pass
        // if `target` were wrongly dropped from required[]).
        $this->assertInvalidAgainst('Action', ['id' => 'a', 'label' => 'A', 'intent' => 'primary']);
        // PageContract without `version`.
        $this->assertInvalidAgainst('PageContract', [
            'shell' => 'product',
            'page' => ['key' => 'k', 'title' => 'T'],
            'layout' => ['template' => 'stack', 'regions' => []],
        ]);
    }

    // ── Negative: discriminated-union + additionalProperties:false strictness ──

    #[Test]
    public function testActionTargetDiscriminatorAndStrictnessAreEnforced(): void
    {
        // Sanity: a well-formed link target validates, so the rejections below
        // are meaningful (not an all-reject schema).
        $this->assertValidAgainst('ActionTarget', ['kind' => 'link', 'href' => '/x']);

        // Foreign branch key: `route` belongs to the route branch, not link; the
        // closed link branch (additionalProperties:false) must REJECT it. Guards
        // against a branch's required[]/additionalProperties silently regressing
        // and widening what the React/framework codegen accepts.
        $this->assertInvalidAgainst('ActionTarget', ['kind' => 'link', 'route' => 'x']);
        // Unknown discriminator: no oneOf branch has a `kind` const of 'bogus'.
        $this->assertInvalidAgainst('ActionTarget', ['kind' => 'bogus']);
        // additionalProperties:false bites: a valid link carrying an extra key.
        $this->assertInvalidAgainst('ActionTarget', ['kind' => 'link', 'href' => '/x', 'evil' => 1]);
    }

    /**
     * Each emitted bundle is itself a well-formed schema opis can compile and
     * use (a malformed $def would throw here).
     */
    #[Test]
    #[DataProvider('emittedBundleProvider')]
    public function testBundleCompiles(string $method): void
    {
        $bundle = json_decode(json_encode(SchemaRegistry::{$method}()) ?: 'null');
        // Validating an empty object against the bundle forces opis to compile the
        // whole schema graph; a structurally broken $def raises a SchemaException.
        $result = (new Validator())->validate(new stdClass(), $bundle);
        self::assertNotNull($result);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function emittedBundleProvider(): array
    {
        return ['page-contract.json' => ['pageContract'], 'fragment.json' => ['fragment']];
    }

    /**
     * Validate a serialized value against one registered $def, with the full
     * registry available so internal $refs resolve.
     */
    private function assertValidAgainst(string $defName, array|JsonSerializable $value): void
    {
        $serialized = $value instanceof JsonSerializable ? $value->jsonSerialize() : $value;

        // json_decode(json_encode(...)) gives the exact wire JSON (empty [] vs {}).
        $data = json_decode(json_encode($serialized) ?: 'null');
        $schema = json_decode(json_encode([
            '$ref' => '#/$defs/' . $defName,
            '$defs' => SchemaRegistry::defs(),
        ]) ?: 'null');

        $result = (new Validator())->validate($data, $schema);

        if (!$result->isValid()) {
            $error = $result->error();
            $formatted = $error instanceof ValidationError ? (new ErrorFormatter())->format($error) : [];
            self::fail($defName . ' serialize() did not validate against its jsonSchema():
'
                . json_encode($formatted, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }

        self::assertTrue($result->isValid());
    }

    /**
     * Assert a raw payload is REJECTED by a registered $def — guards required[]
     * against silent over-loosening, which a forward-only roundtrip cannot.
     *
     * @param array<string, mixed> $value
     */
    private function assertInvalidAgainst(string $defName, array $value): void
    {
        $data = json_decode(json_encode($value) ?: 'null');
        $schema = json_decode(json_encode([
            '$ref' => '#/$defs/' . $defName,
            '$defs' => SchemaRegistry::defs(),
        ]) ?: 'null');

        $result = (new Validator())->validate($data, $schema);

        self::assertFalse(
            $result->isValid(),
            $defName . ' must reject a payload missing a required key (required[] over-loosened?).',
        );
    }
}

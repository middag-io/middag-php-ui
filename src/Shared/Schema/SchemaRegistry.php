<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Shared\Schema;

use Middag\Ui\Action\Action;
use Middag\Ui\Action\ActionResult;
use Middag\Ui\Action\ActionTarget;
use Middag\Ui\Action\Confirmation;
use Middag\Ui\Block\BlockDescriptor;
use Middag\Ui\Block\ChartSeries;
use Middag\Ui\Block\LayoutDescriptor;
use Middag\Ui\Envelope\ContractEnvelopeInterface;
use Middag\Ui\Form\FieldConstraints;
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
use Middag\Ui\Shared\Data\Identity;
use Middag\Ui\Shared\Data\Label;
use Middag\Ui\Shared\Data\Notification;
use Middag\Ui\Shared\Data\Translatable;
use Middag\Ui\Shared\Data\UserPreferences;
use Middag\Ui\Shared\Enum;
use Middag\Ui\Shared\Enum\ActionIntent;
use Middag\Ui\Shared\Enum\ActionTargetKind;
use Middag\Ui\Shared\Enum\ChartType;
use Middag\Ui\Shared\Enum\ConditionOperator;
use Middag\Ui\Shared\Enum\FieldType;
use Middag\Ui\Shared\Enum\FilterType;
use Middag\Ui\Shared\Enum\FragmentKind;
use Middag\Ui\Shared\Enum\HttpMethod;
use Middag\Ui\Shared\Enum\NotificationLevel;
use Middag\Ui\Shared\Enum\RegionUpdateMode;
use Middag\Ui\Shared\Enum\RenderTarget;
use Middag\Ui\Shared\Enum\ThemeMode;
use Middag\Ui\Shared\Enum\ValueFormat;
use Middag\Ui\Table\Column;
use Middag\Ui\Table\FilterDefinition;
use Middag\Ui\Table\Pagination;
use Middag\Ui\Table\TableConfig;
use Middag\Ui\Table\TableOptions;

/**
 * Collects the hand-authored `jsonSchema()` of every wire value object and enum
 * into a single JSON Schema (draft 2020-12) bundle.
 *
 * The schema is the canonical machine-readable wire contract: PHP VOs
 * are the source of truth, this registry bundles their schemas, and the emitter
 * (bin/emit-schemas.php) writes them for downstream codegen (TS types, zod) and
 * the MCP server. Each VO co-locates its `jsonSchema()` with its
 * `jsonSerialize()`; SchemaRoundtripTest guarantees the two never drift.
 *
 * @internal
 */
final class SchemaRegistry
{
    public const JSON_SCHEMA_DRAFT = 'https://json-schema.org/draft/2020-12/schema';

    public const ID_BASE = 'https://ui-docs.middag.io/schemas/';

    /**
     * Every type that contributes a `$def`, keyed by its schema name (the basename
     * used in `#/$defs/<Name>` references). The page/fragment roots live here too.
     *
     * @var array<string, class-string>
     */
    private const TYPES = [
        // Envelope roots.
        'PageContract' => PageContract::class,
        'Fragment' => Fragment::class,
        // Shared label primitives.
        'Label' => Label::class,
        'Translatable' => Translatable::class,
        // Page + layout.
        'PageMeta' => PageMeta::class,
        'LayoutDescriptor' => LayoutDescriptor::class,
        'BlockDescriptor' => BlockDescriptor::class,
        'Breadcrumb' => Breadcrumb::class,
        'InspectorDescriptor' => InspectorDescriptor::class,
        // Actions.
        'Action' => Action::class,
        'ActionTarget' => ActionTarget::class,
        'Confirmation' => Confirmation::class,
        // Notifications + resources.
        'Notification' => Notification::class,
        'PageResources' => PageResources::class,
        'UserPreferences' => UserPreferences::class,
        'Identity' => Identity::class,
        'Branding' => Branding::class,
        // Navigation.
        'NavigationNode' => NavigationNode::class,
        // Tables.
        'TableConfig' => TableConfig::class,
        'TableOptions' => TableOptions::class,
        'Column' => Column::class,
        'FilterDefinition' => FilterDefinition::class,
        'Pagination' => Pagination::class,
        // Polling.
        'PollConfig' => PollConfig::class,
        // Charts + tabs + forms.
        'ChartSeries' => ChartSeries::class,
        'Tab' => Tab::class,
        'FormStep' => FormStep::class,
        'FieldConstraints' => FieldConstraints::class,
        // Server-push slices.
        'RegionUpdate' => RegionUpdate::class,
        'ActionResult' => ActionResult::class,
        'ResourcePatch' => ResourcePatch::class,
        // Enums.
        'ActionIntent' => ActionIntent::class,
        'ActionTargetKind' => ActionTargetKind::class,
        'ChartType' => ChartType::class,
        'ConditionOperator' => ConditionOperator::class,
        'FieldType' => FieldType::class,
        'FilterType' => FilterType::class,
        'FragmentKind' => FragmentKind::class,
        'HttpMethod' => HttpMethod::class,
        'NotificationLevel' => NotificationLevel::class,
        'RegionUpdateMode' => RegionUpdateMode::class,
        'RenderTarget' => RenderTarget::class,
        'ThemeMode' => ThemeMode::class,
        'ValueFormat' => ValueFormat::class,
    ];

    /**
     * The `$defs` map: every type name → its JSON Schema fragment.
     *
     * @return array<string, array<string, mixed>>
     */
    public static function defs(): array
    {
        $defs = [];
        foreach (self::TYPES as $name => $fqcn) {
            /** @var array<string, mixed> $schema */
            $schema = $fqcn::jsonSchema();
            $defs[$name] = $schema;
        }

        return $defs;
    }

    /**
     * Build a self-contained bundle whose root references one `$def`.
     *
     * @return array<string, mixed>
     */
    public static function bundle(string $rootName, string $file, string $title, string $description): array
    {
        return [
            '$schema' => self::JSON_SCHEMA_DRAFT,
            '$id' => self::ID_BASE . $file,
            'title' => $title,
            'description' => $description,
            'x-contract-version' => ContractEnvelopeInterface::VERSION,
            '$ref' => '#/$defs/' . $rootName,
            '$defs' => self::defs(),
        ];
    }

    /**
     * The full page wire contract (PageContract envelope as root).
     *
     * @return array<string, mixed>
     */
    public static function pageContract(): array
    {
        return self::bundle(
            'PageContract',
            'page-contract.json',
            'MIDDAG Page Contract',
            'Wire contract for a full page render. Generated from middag-io/ui PHP value objects (jsonSchema()). Source of truth for the TypeScript types and zod schema in @middag-io/react.',
        );
    }

    /**
     * The fragment wire contract (Fragment envelope as root) for server-push slices.
     *
     * @return array<string, mixed>
     */
    public static function fragment(): array
    {
        return self::bundle(
            'Fragment',
            'fragment.json',
            'MIDDAG Fragment Contract',
            'Wire contract for a server-push fragment (partial update). Generated from middag-io/ui PHP value objects (jsonSchema()).',
        );
    }

    /**
     * Bundle file name → bundle, for the emitter to iterate.
     *
     * @return array<string, array<string, mixed>>
     */
    public static function bundles(): array
    {
        return [
            'page-contract.json' => self::pageContract(),
            'fragment.json' => self::fragment(),
        ];
    }

    /**
     * All registered FQCNs (for coverage tests).
     *
     * @return array<string, class-string>
     */
    public static function types(): array
    {
        return self::TYPES;
    }
}

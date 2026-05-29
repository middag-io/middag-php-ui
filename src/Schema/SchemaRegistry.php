<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Schema;

use Middag\Ui\Contract\ContractEnvelopeInterface;
use Middag\Ui\Data\Action;
use Middag\Ui\Data\ActionResult;
use Middag\Ui\Data\ActionTarget;
use Middag\Ui\Data\BlockDescriptor;
use Middag\Ui\Data\Branding;
use Middag\Ui\Data\Breadcrumb;
use Middag\Ui\Data\ChartSeries;
use Middag\Ui\Data\Column;
use Middag\Ui\Data\Confirmation;
use Middag\Ui\Data\FieldConstraints;
use Middag\Ui\Data\FilterDefinition;
use Middag\Ui\Data\FormStep;
use Middag\Ui\Data\Fragment;
use Middag\Ui\Data\Identity;
use Middag\Ui\Data\InspectorDescriptor;
use Middag\Ui\Data\Label;
use Middag\Ui\Data\LayoutDescriptor;
use Middag\Ui\Data\NavigationNode;
use Middag\Ui\Data\Notification;
use Middag\Ui\Data\PageMeta;
use Middag\Ui\Data\PageResources;
use Middag\Ui\Data\Pagination;
use Middag\Ui\Data\PollConfig;
use Middag\Ui\Data\RegionUpdate;
use Middag\Ui\Data\ResourcePatch;
use Middag\Ui\Data\Tab;
use Middag\Ui\Data\TableConfig;
use Middag\Ui\Data\TableOptions;
use Middag\Ui\Data\Translatable;
use Middag\Ui\Data\UserPreferences;
use Middag\Ui\Enum;
use Middag\Ui\Enum\ActionIntent;
use Middag\Ui\Enum\ActionTargetKind;
use Middag\Ui\Enum\ChartType;
use Middag\Ui\Enum\ConditionOperator;
use Middag\Ui\Enum\FieldType;
use Middag\Ui\Enum\FilterType;
use Middag\Ui\Enum\FragmentKind;
use Middag\Ui\Enum\HttpMethod;
use Middag\Ui\Enum\NotificationLevel;
use Middag\Ui\Enum\RegionUpdateMode;
use Middag\Ui\Enum\RenderTarget;
use Middag\Ui\Enum\ThemeMode;
use Middag\Ui\Enum\ValueFormat;
use Middag\Ui\PageContract;

/**
 * Collects the hand-authored `jsonSchema()` of every wire value object and enum
 * into a single JSON Schema (draft 2020-12) bundle.
 *
 * The schema is the canonical machine-readable wire contract (D-02:A): PHP VOs
 * are the source of truth, this registry bundles their schemas, and the emitter
 * (bin/emit-schemas.php) writes them for downstream codegen (TS types, zod) and
 * the MCP server. Each VO co-locates its `jsonSchema()` with its
 * `jsonSerialize()`; SchemaRoundtripTest guarantees the two never drift.
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

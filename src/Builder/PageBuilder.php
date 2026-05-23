<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.com.br>
 * @copyright   2026 MIDDAG (https://www.middag.com.br)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Builder;

use Closure;
use Middag\Ui\Contract\BlockDescriptorInterface;
use Middag\Ui\Contract\BreadcrumbInterface;
use Middag\Ui\Contract\InspectorDescriptorInterface;
use Middag\Ui\Contract\PageActionInterface;
use Middag\Ui\Contract\PageBuilderInterface;
use Middag\Ui\Data\InspectorDescriptor;
use Middag\Ui\Data\LayoutDescriptor;
use Middag\Ui\Data\PageAction;
use Middag\Ui\Data\PageContractData;
use Middag\Ui\Data\PageMeta;

/**
 * Fluent builder for PageContractData (L3 API — ADR-807).
 *
 * Produces a PageContractData from a chainable API.
 *
 * Usage:
 *   PageBuilder::page('segments.index')
 *       ->title('Segments')
 *       ->layout('stack')
 *       ->region('content', [Block::dense_table(...)])
 *       ->build();
 *
 * @internal
 */
class PageBuilder implements PageBuilderInterface
{
    private string $title = '';

    private ?string $subtitle = null;

    private string $shell = 'product';

    private string $layoutTemplate = 'stack';

    /** @var array<string, mixed> */
    private array $layoutMeta = [];

    /** @var array<string, BlockDescriptorInterface[]> */
    private array $regions = [];

    /** @var BreadcrumbInterface[] */
    private array $breadcrumbs = [];

    /** @var PageActionInterface[] */
    private array $actions = [];

    private bool $isOverlay = false;

    private ?array $helpData = null;

    private ?InspectorDescriptor $inspector = null;

    private function __construct(
        private readonly string $key,
    ) {}

    /**
     * Start building a page.
     */
    public static function page(string $key): self
    {
        return new self($key);
    }

    /**
     * Start a CRUD convention builder (Levels 1-2 — ADR-807).
     *
     * Level 1: PageBuilder::crud(Segment::class)->build('index')
     * Level 2: PageBuilder::crud(Segment::class)->without('show')->columns([...])->build('index')
     */
    public static function crud(string $entity_class): CrudBuilder
    {
        return CrudBuilder::for($entity_class);
    }

    /**
     * Create a PageAction (convenience factory, avoids importing PageAction directly).
     */
    public static function action(
        string $id,
        string $label,
        string $intent = 'secondary',
        ?string $href = null,
        ?string $method = null,
        ?string $icon = null,
    ): PageAction {
        return new PageAction(
            id: $id,
            label: $label,
            intent: $intent,
            href: $href,
            method: $method,
            icon: $icon,
        );
    }

    public function title(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function subtitle(string $subtitle): static
    {
        $this->subtitle = $subtitle;

        return $this;
    }

    public function shell(string $shell): static
    {
        $this->shell = $shell;

        return $this;
    }

    public function layout(string $template): static
    {
        $this->layoutTemplate = $template;

        return $this;
    }

    /**
     * Set layout-level metadata (e.g. wizard steps).
     *
     * @param array<string, mixed> $meta
     */
    public function meta(array $meta): static
    {
        $this->layoutMeta = $meta;

        return $this;
    }

    /**
     * Add a layout region with blocks.
     *
     * Accepts either a pre-built array of BlockDescriptor objects or a
     * Closure that receives a {@see RegionBuilder} for fluent composition.
     *
     * @param string                             $name   Region name (content, aside, header, footer, etc.)
     * @param BlockDescriptorInterface[]|Closure $blocks Ordered blocks or builder callback
     */
    public function region(string $name, array|Closure $blocks): static
    {
        if ($blocks instanceof Closure) {
            $builder = new RegionBuilder();
            $blocks($builder);
            $this->regions[$name] = $builder->all();
        } else {
            $this->regions[$name] = $blocks;
        }

        return $this;
    }

    /**
     * Set breadcrumbs via a builder callback.
     *
     * Usage: ->breadcrumbs(fn($bc) => $bc->item('Home', '/')->current('Page'))
     */
    public function breadcrumbs(Closure $callback): static
    {
        $builder = new BreadcrumbListBuilder();
        $callback($builder);
        $this->breadcrumbs = $builder->all();

        return $this;
    }

    /**
     * Set page-level actions.
     *
     * @param PageActionInterface[] $actions
     */
    public function actions(array $actions): static
    {
        $this->actions = $actions;

        return $this;
    }

    /**
     * Mark this page as an overlay (full-screen panel with X close).
     */
    public function overlay(): static
    {
        $this->isOverlay = true;

        return $this;
    }

    /**
     * Add contextual help data (rendered in the HelpPanel slide-out).
     */
    public function help(string $title, string $body, ?string $learn_more = null): static
    {
        $this->helpData = [
            'title' => $title,
            'body' => $body,
            'learnMore' => $learn_more,
        ];

        return $this;
    }

    /**
     * Declare an inspector side-panel for this page.
     *
     * The endpoint URL should contain `{id}` as a placeholder for the
     * selected item identifier (replaced client-side on selection).
     *
     * @param string $endpoint API endpoint URL with {id} placeholder
     * @param int    $width    Panel width in pixels (default 440)
     */
    public function inspector(string $endpoint, int $width = 440): static
    {
        $this->inspector = new InspectorDescriptor(
            endpoint: $endpoint,
            width: $width,
        );

        return $this;
    }

    /**
     * Build the PageContractData.
     */
    public function build(): PageContractData
    {
        return new PageContractData(
            shell: $this->shell,
            page: new PageMeta(
                key: $this->key,
                title: $this->title,
                subtitle: $this->subtitle,
                breadcrumbs: $this->breadcrumbs,
                actions: $this->actions,
            ),
            layout: new LayoutDescriptor(
                template: $this->layoutTemplate,
                regions: $this->regions,
                meta: $this->layoutMeta,
            ),
        );
    }

    /**
     * Build Inertia props array (contract + overlay + help as separate props).
     *
     * Use this when you need overlay/help metadata alongside the contract.
     * The PageContractData itself remains unchanged (@api safe).
     *
     * @return array{contract: PageContractData, overlay?: bool, help?: array, inspector?: InspectorDescriptorInterface}
     */
    public function toProps(): array
    {
        $props = [
            'contract' => $this->build(),
        ];

        if ($this->isOverlay) {
            $props['overlay'] = true;
        }

        if ($this->helpData !== null) {
            $props['help'] = $this->helpData;
        }

        if ($this->inspector instanceof InspectorDescriptorInterface) {
            $props['inspector'] = $this->inspector;
        }

        return $props;
    }
}

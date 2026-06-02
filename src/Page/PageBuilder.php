<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Page;

use Closure;
use Middag\Ui\Action\Action;
use Middag\Ui\Action\ActionInterface;
use Middag\Ui\Action\ActionTarget;
use Middag\Ui\Block\BlockBuilder;
use Middag\Ui\Block\BlockDescriptorInterface;
use Middag\Ui\Block\LayoutDescriptor;
use Middag\Ui\Inspector\InspectorDescriptor;
use Middag\Ui\Inspector\InspectorDescriptorInterface;
use Middag\Ui\Navigation\BreadcrumbInterface;
use Middag\Ui\Navigation\BreadcrumbListBuilder;
use Middag\Ui\Region\RegionBuilder;
use Middag\Ui\Shared\Data\Notification;
use Middag\Ui\Shared\Data\Translatable;
use Middag\Ui\Shared\Enum\ActionIntent;
use Middag\Ui\Shared\Enum\NotificationLevel;
use Middag\Ui\Table\CrudBuilder;

/**
 * Fluent builder for PageContract (L3 API).
 *
 * Produces a PageContract from a chainable API.
 *
 * Usage:
 *   PageBuilder::page('segments.index')
 *       ->title('Segments')
 *       ->layout('stack')
 *       ->region('content', [BlockBuilder::denseTable(...)])
 *       ->build();
 *
 * @api
 */
class PageBuilder implements PageBuilderInterface
{
    private string|Translatable $title = '';

    private string|Translatable|null $subtitle = null;

    private string $shell = 'product';

    private string $layoutTemplate = 'stack';

    /** @var array<string, mixed> */
    private array $layoutMeta = [];

    /** @var array<string, BlockDescriptorInterface[]> */
    private array $regions = [];

    /** @var BreadcrumbInterface[] */
    private array $breadcrumbs = [];

    /** @var ActionInterface[] */
    private array $actions = [];

    private bool $isOverlay = false;

    private ?array $helpData = null;

    private ?InspectorDescriptor $inspector = null;

    /** @var Notification[] */
    private array $notifications = [];

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
     * Start a CRUD convention builder (Levels 1-2).
     *
     * Level 1: PageBuilder::crud(Segment::class)->build('index')
     * Level 2: PageBuilder::crud(Segment::class)->without('show')->columns([...])->build('index')
     */
    public static function crud(string $entityClass): CrudBuilder
    {
        return CrudBuilder::for($entityClass);
    }

    /**
     * Create an Action (convenience factory, avoids importing Action directly).
     */
    public static function action(
        string $id,
        string|Translatable $label,
        ActionTarget $target,
        ActionIntent $intent = ActionIntent::SECONDARY,
        ?string $icon = null,
    ): Action {
        return new Action(
            id: $id,
            label: $label,
            target: $target,
            intent: $intent,
            icon: $icon,
        );
    }

    public function title(string|Translatable $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function subtitle(string|Translatable $subtitle): static
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
     * @param ActionInterface[] $actions
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
    public function help(string $title, string $body, ?string $learnMore = null): static
    {
        $this->helpData = [
            'title' => $title,
            'body' => $body,
            'learnMore' => $learnMore,
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
     * Attach a notification (flash / toast) to the page.
     */
    public function notify(Notification $notification): static
    {
        $this->notifications[] = $notification;

        return $this;
    }

    /**
     * Attach a success notification.
     */
    public function notifySuccess(string|Translatable $message, string|Translatable|null $title = null): static
    {
        return $this->notify(new Notification(NotificationLevel::SUCCESS, $message, $title));
    }

    /**
     * Attach an info notification.
     */
    public function notifyInfo(string|Translatable $message, string|Translatable|null $title = null): static
    {
        return $this->notify(new Notification(NotificationLevel::INFO, $message, $title));
    }

    /**
     * Attach a warning notification.
     */
    public function notifyWarning(string|Translatable $message, string|Translatable|null $title = null): static
    {
        return $this->notify(new Notification(NotificationLevel::WARNING, $message, $title));
    }

    /**
     * Attach an error notification.
     */
    public function notifyError(string|Translatable $message, string|Translatable|null $title = null): static
    {
        return $this->notify(new Notification(NotificationLevel::ERROR, $message, $title));
    }

    /**
     * Build the PageContract.
     */
    public function build(): PageContract
    {
        return new PageContract(
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
            notifications: $this->notifications,
        );
    }

    /**
     * Build a props array (contract + overlay + help as separate props).
     *
     * Use this when you need overlay/help metadata alongside the contract.
     * The PageContract itself remains unchanged (@api safe).
     *
     * @return array{contract: PageContract, overlay?: bool, help?: array, inspector?: InspectorDescriptor}
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

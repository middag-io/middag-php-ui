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
use Middag\Ui\Shared\Data\Notification;
use Middag\Ui\Shared\Data\Translatable;

/** @api */
interface PageBuilderInterface
{
    public function title(string|Translatable $title): static;

    public function subtitle(string|Translatable $subtitle): static;

    public function shell(string $shell): static;

    public function layout(string $template): static;

    /**
     * Set layout-level metadata (e.g. wizard steps).
     *
     * @param array<string, mixed> $meta
     */
    public function meta(array $meta): static;

    public function region(string $name, array|Closure $blocks): static;

    public function breadcrumbs(Closure $callback): static;

    public function actions(array $actions): static;

    public function overlay(): static;

    public function help(string $title, string $body, ?string $learnMore = null): static;

    public function inspector(string $endpoint, int $width = 440): static;

    /**
     * Attach a notification (flash / toast) to the page.
     */
    public function notify(Notification $notification): static;

    /**
     * Attach a success notification.
     */
    public function notifySuccess(string|Translatable $message, string|Translatable|null $title = null): static;

    /**
     * Attach an info notification.
     */
    public function notifyInfo(string|Translatable $message, string|Translatable|null $title = null): static;

    /**
     * Attach a warning notification.
     */
    public function notifyWarning(string|Translatable $message, string|Translatable|null $title = null): static;

    /**
     * Attach an error notification.
     */
    public function notifyError(string|Translatable $message, string|Translatable|null $title = null): static;

    public function build(): PageContractInterface;

    public function toProps(): array;
}

<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.com.br>
 * @copyright   2026 MIDDAG (https://www.middag.com.br)
 * @license     proprietary
 */

namespace Middag\Ui\Contract;

/**
 * Contract for UI widgets.
 *
 * Widgets prepare data for a host template (Moodle Mustache, WordPress, etc.)
 * and optionally a JS mount point, exposing rendering helpers used by
 * controllers and blocks.
 *
 * @api
 */
interface WidgetInterface
{
    /**
     * Set the unique identifier for this widget instance.
     * Needed for DOM ID generation and JS mounting points.
     */
    public function setId(string $id): void;

    /**
     * Get the unique identifier.
     */
    public function getId(): string;

    /**
     * Prepare and return the data structure required by the template/component.
     *
     * @return array<string, mixed>
     */
    public function getData(): array;

    /**
     * Render the widget's HTML output.
     *
     * @return string HTML
     */
    public function render(): string;
}

<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Block;

/**
 * Contract for renderable UI blocks.
 *
 * Implementations prepare data for templates and expose helpers for rendering
 * and HTML attributes used by block drivers. Distinct from BlockDescriptorInterface
 * (descriptor is page-composition metadata; BlockInterface is the runtime contract).
 *
 * @api
 */
interface BlockInterface
{
    /**
     * Compute and store the block title.
     */
    public function setTitle(): void;

    /**
     * Returns the human-readable title of the block.
     * Should call set_title() lazily if not already set.
     *
     * @return string
     */
    public function getTitle(): string;

    /**
     * Main computation function.
     * Should return all data needed by the template.
     *
     * @return array<string, mixed>
     */
    public function processContent(): array;

    /**
     * Returns the processed block data, with memoization.
     *
     * @return array<string, mixed>
     */
    public function getContent(): array;

    /**
     * Defines an HTML attribute for the block output wrapper.
     *
     * @param string $key
     * @param string $value
     */
    public function setAttribute(string $key, string $value): void;

    /**
     * Render the block using the associated widget and template system.
     *
     * @return string Rendered HTML fragment
     */
    public function render(): string;
}

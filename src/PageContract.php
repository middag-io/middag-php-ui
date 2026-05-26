<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui;

use Middag\Ui\Contract\PageContractInterface;
use Middag\Ui\Data\LayoutDescriptor;
use Middag\Ui\Data\PageMeta;
use Middag\Ui\Data\PageResources;

/**
 * Top-level page contract envelope (ADR-807).
 *
 * Describes a complete server-driven page: shell, page identity,
 * layout with regions and blocks, and shared resources.
 * Serialized as Inertia props for React rendering.
 */
readonly class PageContract implements PageContractInterface
{
    public const VERSION = '1';

    public function __construct(
        public string $shell,
        public PageMeta $page,
        public LayoutDescriptor $layout,
        public ?PageResources $resources = null,
    ) {}

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        $data = [
            'version' => self::VERSION,
            'shell' => $this->shell,
            'page' => $this->page->jsonSerialize(),
            'layout' => $this->layout->jsonSerialize(),
        ];

        if ($this->resources instanceof PageResources) {
            $data['resources'] = $this->resources->jsonSerialize();
        }

        return $data;
    }
}

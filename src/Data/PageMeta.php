<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.com.br>
 * @copyright   2026 MIDDAG (https://www.middag.com.br)
 * @license     proprietary
 */

namespace Middag\Ui\Data;

use Middag\Ui\Contract\BreadcrumbInterface;
use Middag\Ui\Contract\PageActionInterface;
use Middag\Ui\Contract\PageMetaInterface;

readonly class PageMeta implements PageMetaInterface
{
    /**
     * @param array<BreadcrumbInterface> $breadcrumbs
     * @param array<PageActionInterface> $actions
     */
    public function __construct(
        public string $key,
        public string $title,
        public ?string $subtitle = null,
        public array $breadcrumbs = [],
        public array $actions = [],
    ) {}

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        $payload = [
            'key' => $this->key,
            'title' => $this->title,
        ];

        if ($this->subtitle !== null) {
            $payload['subtitle'] = $this->subtitle;
        }

        if ($this->breadcrumbs !== []) {
            $payload['breadcrumbs'] = array_map(
                static fn (BreadcrumbInterface $breadcrumb): array => $breadcrumb->jsonSerialize(),
                $this->breadcrumbs,
            );
        }

        if ($this->actions !== []) {
            $payload['actions'] = array_map(
                static fn (PageActionInterface $action): array => $action->jsonSerialize(),
                $this->actions,
            );
        }

        return $payload;
    }
}

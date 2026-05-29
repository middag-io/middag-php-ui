<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Data;

use Middag\Ui\Contract\BreadcrumbInterface;
use Middag\Ui\Contract\PageActionInterface;
use Middag\Ui\Contract\PageMetaInterface;
use Middag\Ui\Support\Label;

readonly class PageMeta implements PageMetaInterface
{
    /**
     * @param array<BreadcrumbInterface> $breadcrumbs
     * @param array<PageActionInterface> $actions
     */
    public function __construct(
        public string $key,
        public string|Translatable $title,
        public string|Translatable|null $subtitle = null,
        public array $breadcrumbs = [],
        public array $actions = [],
    ) {}

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        $payload = [
            'key' => $this->key,
            'title' => Label::serialize($this->title),
        ];

        if ($this->subtitle !== null) {
            $payload['subtitle'] = Label::serializeNullable($this->subtitle);
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

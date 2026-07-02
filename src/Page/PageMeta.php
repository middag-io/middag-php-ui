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

use Middag\Ui\Action\Contract\ActionInterface;
use Middag\Ui\Navigation\Contract\BreadcrumbInterface;
use Middag\Ui\Page\Contract\PageMetaInterface;
use Middag\Ui\Shared\ValueObject\Label;
use Middag\Ui\Shared\ValueObject\Translatable;

/**
 * Page identity: key, title, subtitle, breadcrumbs, and page-level actions.
 *
 * @api
 */
readonly class PageMeta implements PageMetaInterface
{
    /**
     * @param array<BreadcrumbInterface> $breadcrumbs
     * @param array<ActionInterface>     $actions
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
                static fn (ActionInterface $action): array => $action->jsonSerialize(),
                $this->actions,
            );
        }

        return $payload;
    }

    /** @return array<string, mixed> */
    public static function jsonSchema(): array
    {
        return ['type' => 'object', 'required' => ['key', 'title'],
            'properties' => ['key' => ['type' => 'string'], 'title' => ['$ref' => '#/$defs/Label'], 'subtitle' => ['$ref' => '#/$defs/Label'], 'breadcrumbs' => ['type' => 'array', 'items' => ['$ref' => '#/$defs/Breadcrumb']], 'actions' => ['type' => 'array', 'items' => ['$ref' => '#/$defs/Action']]],
            'additionalProperties' => false];
    }
}

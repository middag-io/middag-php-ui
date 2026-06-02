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

use Middag\Ui\Block\LayoutDescriptor;
use Middag\Ui\Shared\Data\Notification;

/**
 * Top-level page contract envelope.
 *
 * Describes a complete server-driven page: shell, page identity,
 * layout with regions and blocks, and shared resources. Serialized to JSON
 * and consumed by the client renderer.
 *
 * @api
 */
readonly class PageContract implements PageContractInterface
{
    /**
     * @param Notification[] $notifications
     */
    public function __construct(
        public string $shell,
        public PageMeta $page,
        public LayoutDescriptor $layout,
        public ?PageResources $resources = null,
        public array $notifications = [],
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

        if ($this->notifications !== []) {
            $data['notifications'] = array_map(
                static fn (Notification $notification): array => $notification->jsonSerialize(),
                $this->notifications,
            );
        }

        return $data;
    }

    /** @return array<string, mixed> */
    public static function jsonSchema(): array
    {
        return ['type' => 'object', 'required' => ['version', 'shell', 'page', 'layout'],
            'properties' => ['version' => ['const' => '1'], 'shell' => ['type' => 'string'],
                'page' => ['$ref' => '#/$defs/PageMeta'], 'layout' => ['$ref' => '#/$defs/LayoutDescriptor'],
                'resources' => ['$ref' => '#/$defs/PageResources'],
                'notifications' => ['type' => 'array', 'items' => ['$ref' => '#/$defs/Notification']]],
            'additionalProperties' => false];
    }
}

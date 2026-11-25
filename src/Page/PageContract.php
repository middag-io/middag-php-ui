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
use Middag\Ui\Inspector\Contract\InspectorDescriptorInterface;
use Middag\Ui\Page\Contract\PageContractInterface;
use Middag\Ui\Shared\ValueObject\Notification;

/**
 * Top-level page contract envelope.
 *
 * Describes a complete server-driven page: shell, page identity, layout with
 * regions and blocks, notifications, the entity route map, and the optional
 * contextual `help` and `inspector` side panel. Serialized to JSON and
 * consumed by the client renderer.
 *
 * @api
 */
readonly class PageContract implements PageContractInterface
{
    /**
     * @param Notification[]                                                   $notifications
     * @param array<string, string>                                            $entities      entity type → route pattern map; blocks resolve entity refs against it
     * @param null|array{title: string, body: string, learnMore?: null|string} $help          contextual help (title, body, optional learn-more link)
     */
    public function __construct(
        public string $shell,
        public PageMeta $page,
        public LayoutDescriptor $layout,
        public array $notifications = [],
        public array $entities = [],
        public ?array $help = null,
        public ?InspectorDescriptorInterface $inspector = null,
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

        if ($this->notifications !== []) {
            $data['notifications'] = array_map(
                static fn (Notification $notification): array => $notification->jsonSerialize(),
                $this->notifications,
            );
        }

        if ($this->entities !== []) {
            $data['entities'] = $this->entities;
        }

        if ($this->help !== null) {
            $data['help'] = array_filter(
                ['title' => $this->help['title'], 'body' => $this->help['body'], 'learnMore' => $this->help['learnMore'] ?? null],
                static fn (?string $value): bool => $value !== null,
            );
        }

        if ($this->inspector instanceof InspectorDescriptorInterface) {
            $data['inspector'] = $this->inspector->jsonSerialize();
        }

        return $data;
    }

    /** @return array<string, mixed> */
    public static function jsonSchema(): array
    {
        return ['type' => 'object', 'required' => ['version', 'shell', 'page', 'layout'],
            'properties' => ['version' => ['const' => '2'], 'shell' => ['type' => 'string'],
                'page' => ['$ref' => '#/$defs/PageMeta'], 'layout' => ['$ref' => '#/$defs/LayoutDescriptor'],
                'notifications' => ['type' => 'array', 'items' => ['$ref' => '#/$defs/Notification']],
                'entities' => ['type' => 'object', 'additionalProperties' => ['type' => 'string']],
                'help' => ['type' => 'object', 'required' => ['title', 'body'],
                    'properties' => ['title' => ['type' => 'string'], 'body' => ['type' => 'string'], 'learnMore' => ['type' => 'string']],
                    'additionalProperties' => false],
                'inspector' => ['$ref' => '#/$defs/InspectorDescriptor']],
            'additionalProperties' => false];
    }
}

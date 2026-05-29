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

use Middag\Ui\Contract\ActionInterface;
use Middag\Ui\Contract\BlockDescriptorInterface;

/**
 * A typed UI block: a `type` discriminator plus its `data` payload and
 * optional title, actions, meta, and poll config.
 *
 * @api
 */
readonly class BlockDescriptor implements BlockDescriptorInterface
{
    /**
     * @param array<string, mixed>   $data
     * @param array<ActionInterface> $actions
     * @param array<string, mixed>   $meta
     */
    public function __construct(
        public string $type,
        public string $key,
        public array $data,
        public ?string $variant = null,
        public string|Translatable|null $title = null,
        public string|Translatable|null $subtitle = null,
        public array $actions = [],
        public array $meta = [],
        public ?PollConfig $poll = null,
    ) {}

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        $payload = [
            'type' => $this->type,
            'key' => $this->key,
            'data' => $this->data,
        ];

        if ($this->variant !== null) {
            $payload['variant'] = $this->variant;
        }

        if ($this->title !== null) {
            $payload['title'] = Label::serializeNullable($this->title);
        }

        if ($this->subtitle !== null) {
            $payload['subtitle'] = Label::serializeNullable($this->subtitle);
        }

        if ($this->actions !== []) {
            $payload['actions'] = array_map(
                static fn (ActionInterface $action): array => $action->jsonSerialize(),
                $this->actions,
            );
        }

        if ($this->meta !== []) {
            $payload['meta'] = $this->meta;
        }

        if ($this->poll instanceof PollConfig) {
            $payload['poll'] = $this->poll->jsonSerialize();
        }

        return $payload;
    }

    /** @return array<string, mixed> */
    public static function jsonSchema(): array
    {
        return ['type' => 'object', 'required' => ['type', 'key', 'data'],
            'properties' => ['type' => ['type' => 'string'], 'key' => ['type' => 'string'], 'data' => ['oneOf' => [['type' => 'object', 'additionalProperties' => true], ['type' => 'array', 'maxItems' => 0]]], 'variant' => ['type' => 'string'], 'title' => ['$ref' => '#/$defs/Label'], 'subtitle' => ['$ref' => '#/$defs/Label'], 'actions' => ['type' => 'array', 'items' => ['$ref' => '#/$defs/Action']], 'meta' => ['type' => 'object', 'additionalProperties' => true], 'poll' => ['$ref' => '#/$defs/PollConfig']],
            'additionalProperties' => false];
    }
}

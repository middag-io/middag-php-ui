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

use Middag\Ui\Action\ActionInterface;
use Middag\Ui\Region\PollConfig;
use Middag\Ui\Shared\Data\Label;
use Middag\Ui\Shared\Data\Translatable;

/**
 * A typed UI block: a `type` discriminator plus its `data` payload and
 * optional title, actions, meta, poll config, deferral, and remember flags.
 *
 * `deferred` marks a block whose data the client fetches after the initial
 * render (Inertia v3 deferred props): the React renderer swaps its hand-rolled
 * reload-on-mount for `<Deferred>`. Additive + omitted when false, so it does
 * not change the wire shape of non-deferred blocks.
 *
 * `remember` marks a block whose local UI state the client persists in history
 * state (Inertia v3 `useRemember`): on back/forward navigation the React
 * renderer restores the block's transient state instead of resetting it.
 * Additive + omitted when false, so it does not change the wire shape of
 * blocks that opt out.
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
        public bool $deferred = false,
        public bool $remember = false,
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

        if ($this->deferred) {
            $payload['deferred'] = true;
        }

        if ($this->remember) {
            $payload['remember'] = true;
        }

        return $payload;
    }

    /** @return array<string, mixed> */
    public static function jsonSchema(): array
    {
        return ['type' => 'object', 'required' => ['type', 'key', 'data'],
            'properties' => ['type' => ['type' => 'string'], 'key' => ['type' => 'string'], 'data' => ['oneOf' => [['type' => 'object', 'additionalProperties' => true], ['type' => 'array', 'maxItems' => 0]]], 'variant' => ['type' => 'string'], 'title' => ['$ref' => '#/$defs/Label'], 'subtitle' => ['$ref' => '#/$defs/Label'], 'actions' => ['type' => 'array', 'items' => ['$ref' => '#/$defs/Action']], 'meta' => ['type' => 'object', 'additionalProperties' => true], 'poll' => ['$ref' => '#/$defs/PollConfig'], 'deferred' => ['type' => 'boolean'], 'remember' => ['type' => 'boolean']],
            'additionalProperties' => false];
    }
}

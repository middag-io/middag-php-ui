<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.com.br>
 * @copyright   2026 MIDDAG (https://www.middag.com.br)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Data;

use Middag\Ui\Contract\BlockDescriptorInterface;
use Middag\Ui\Contract\PageActionInterface;

readonly class BlockDescriptor implements BlockDescriptorInterface
{
    /**
     * @param array<string, mixed>       $data
     * @param array<PageActionInterface> $actions
     * @param array<string, mixed>       $meta
     */
    public function __construct(
        public string $type,
        public string $key,
        public array $data,
        public ?string $variant = null,
        public ?string $title = null,
        public ?string $subtitle = null,
        public array $actions = [],
        public array $meta = [],
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
            $payload['title'] = $this->title;
        }

        if ($this->subtitle !== null) {
            $payload['subtitle'] = $this->subtitle;
        }

        if ($this->actions !== []) {
            $payload['actions'] = array_map(
                static fn (PageActionInterface $action): array => $action->jsonSerialize(),
                $this->actions,
            );
        }

        if ($this->meta !== []) {
            $payload['meta'] = $this->meta;
        }

        return $payload;
    }
}

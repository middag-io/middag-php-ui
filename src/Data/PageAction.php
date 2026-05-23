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

use Middag\Ui\Contract\PageActionInterface;

readonly class PageAction implements PageActionInterface
{
    public function __construct(
        public string $id,
        public string $label,
        public string $intent,
        public ?string $href = null,
        public ?string $method = null,
        public ?string $icon = null,
        public bool $requires_confirmation = false,
        public bool $disabled = false,
        public bool $loading = false,
    ) {}

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        $payload = [
            'id' => $this->id,
            'label' => $this->label,
            'intent' => $this->intent,
        ];

        if ($this->href !== null) {
            $payload['href'] = $this->href;
        }

        if ($this->method !== null) {
            $payload['method'] = $this->method;
        }

        if ($this->icon !== null) {
            $payload['icon'] = $this->icon;
        }

        if ($this->requires_confirmation) {
            $payload['requires_confirmation'] = true;
        }

        if ($this->disabled) {
            $payload['disabled'] = true;
        }

        if ($this->loading) {
            $payload['loading'] = true;
        }

        return $payload;
    }
}

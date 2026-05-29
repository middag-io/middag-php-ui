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

use Middag\Ui\Contract\PageActionInterface;
use Middag\Ui\Support\Label;

readonly class PageAction implements PageActionInterface
{
    public function __construct(
        public string $id,
        public string|Translatable $label,
        public string $intent,
        public ?string $href = null,
        public ?string $method = null,
        public ?string $icon = null,
        public ?Confirmation $confirmation = null,
        public bool $disabled = false,
        public bool $loading = false,
        public ?string $capability = null,
    ) {}

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        $payload = [
            'id' => $this->id,
            'label' => Label::serialize($this->label),
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

        if ($this->confirmation instanceof Confirmation) {
            $payload['confirmation'] = $this->confirmation->jsonSerialize();
        }

        if ($this->disabled) {
            $payload['disabled'] = true;
        }

        if ($this->loading) {
            $payload['loading'] = true;
        }

        if ($this->capability !== null) {
            $payload['capability'] = $this->capability;
        }

        return $payload;
    }
}

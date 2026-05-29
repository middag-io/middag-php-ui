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

use JsonSerializable;
use Middag\Ui\Support\Label;

/**
 * Confirmation modal payload for a destructive or guarded action.
 *
 * @api
 */
final readonly class Confirmation implements JsonSerializable
{
    public function __construct(
        public string|Translatable $title,
        public string|Translatable $message,
        public string|Translatable|null $confirmLabel = null,
        public string|Translatable|null $cancelLabel = null,
        public string $variant = 'default',
    ) {}

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        $payload = [
            'title' => Label::serialize($this->title),
            'message' => Label::serialize($this->message),
            'variant' => $this->variant,
        ];

        if ($this->confirmLabel !== null) {
            $payload['confirmLabel'] = Label::serializeNullable($this->confirmLabel);
        }

        if ($this->cancelLabel !== null) {
            $payload['cancelLabel'] = Label::serializeNullable($this->cancelLabel);
        }

        return $payload;
    }
}

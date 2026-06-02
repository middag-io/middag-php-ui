<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Action;

use JsonSerializable;
use Middag\Ui\Shared\Data\Label;
use Middag\Ui\Shared\Data\Translatable;

/**
 * Confirmation modal payload for a destructive or guarded action.
 *
 * @api
 */
final readonly class Confirmation implements JsonSerializable
{
    /**
     * @param string $variant Visual style of the confirmation modal, interpreted
     *                        by the React client. Recognized values: `default`
     *                        (neutral) and `danger` (destructive). Kept as a free
     *                        string rather than an enum so the client may add
     *                        variants without a PHP-side release; promoting it to
     *                        a typed enum would require an ADR.
     */
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

    /** @return array<string, mixed> */
    public static function jsonSchema(): array
    {
        return ['type' => 'object', 'required' => ['title', 'message', 'variant'],
            'properties' => ['title' => ['$ref' => '#/$defs/Label'], 'message' => ['$ref' => '#/$defs/Label'], 'variant' => ['type' => 'string'], 'confirmLabel' => ['$ref' => '#/$defs/Label'], 'cancelLabel' => ['$ref' => '#/$defs/Label']],
            'additionalProperties' => false];
    }
}

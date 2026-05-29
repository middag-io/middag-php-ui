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
use Middag\Ui\Enum\NotificationLevel;
use Middag\Ui\Support\Label;

/**
 * User-facing feedback message (flash / toast).
 *
 * @api
 */
final readonly class Notification implements JsonSerializable
{
    public function __construct(
        public NotificationLevel $level,
        public string|Translatable $message,
        public string|Translatable|null $title = null,
        public bool $dismissible = true,
        public ?int $timeout = null,
        public ?Action $action = null,
    ) {}

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        $payload = [
            'level' => $this->level->value,
            'message' => Label::serialize($this->message),
            'dismissible' => $this->dismissible,
        ];

        if ($this->title !== null) {
            $payload['title'] = Label::serializeNullable($this->title);
        }

        if ($this->timeout !== null) {
            $payload['timeout'] = $this->timeout;
        }

        if ($this->action instanceof Action) {
            $payload['action'] = $this->action->jsonSerialize();
        }

        return $payload;
    }
}

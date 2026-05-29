<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Shared\Data;

use JsonSerializable;
use Middag\Ui\Action\Action;
use Middag\Ui\Shared\Enum\NotificationLevel;

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

    /** @return array<string, mixed> */
    public static function jsonSchema(): array
    {
        return ['type' => 'object', 'required' => ['level', 'message', 'dismissible'],
            'properties' => ['level' => ['$ref' => '#/$defs/NotificationLevel'], 'message' => ['$ref' => '#/$defs/Label'], 'dismissible' => ['type' => 'boolean'], 'title' => ['$ref' => '#/$defs/Label'], 'timeout' => ['type' => 'integer'], 'action' => ['$ref' => '#/$defs/Action']],
            'additionalProperties' => false];
    }
}

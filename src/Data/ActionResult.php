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
use Middag\Ui\PageContract;

/**
 * Server response to an action/mutation (POST/PUT/DELETE).
 *
 * The {@see PageContract} covers page load; this covers what the
 * server returns after a mutation: notifications to flash, an optional
 * redirect, block keys to refresh, and per-field validation errors.
 *
 * @api
 */
final readonly class ActionResult implements JsonSerializable
{
    /**
     * @param Notification[]                 $notifications
     * @param string[]                       $refreshBlocks Block keys to reload
     * @param array<string, string|string[]> $errors        Per-field validation errors
     */
    public function __construct(
        public bool $success = true,
        public array $notifications = [],
        public ?string $redirect = null,
        public array $refreshBlocks = [],
        public array $errors = [],
    ) {}

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        $payload = [
            'success' => $this->success,
        ];

        if ($this->notifications !== []) {
            $payload['notifications'] = array_map(
                static fn (Notification $notification): array => $notification->jsonSerialize(),
                $this->notifications,
            );
        }

        if ($this->redirect !== null) {
            $payload['redirect'] = $this->redirect;
        }

        if ($this->refreshBlocks !== []) {
            $payload['refreshBlocks'] = $this->refreshBlocks;
        }

        if ($this->errors !== []) {
            $payload['errors'] = $this->errors;
        }

        return $payload;
    }
}

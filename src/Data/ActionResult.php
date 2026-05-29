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
 * redirect, per-field validation errors, and two ways to update the view.
 *
 * Pull vs push for view updates:
 * - `refreshBlocks` (pull) — block keys the client re-fetches itself.
 * - `fragments` (push) — ready pieces the server already built, so the client
 *   swaps them in without a round-trip. Use push when the server holds the
 *   fresh data anyway; use pull when re-fetching is cheaper or simpler.
 *
 * `resources` carries a partial patch to preferences/capabilities/flags when a
 * mutation changes them mid-flow.
 *
 * @api
 */
final readonly class ActionResult implements JsonSerializable
{
    /**
     * @param Notification[]                 $notifications
     * @param string[]                       $refreshBlocks Block keys to reload (pull)
     * @param array<string, string|string[]> $errors        Per-field validation errors
     * @param Fragment[]                     $fragments     Ready pieces to swap in (push)
     */
    public function __construct(
        public bool $success = true,
        public array $notifications = [],
        public ?string $redirect = null,
        public array $refreshBlocks = [],
        public array $errors = [],
        public array $fragments = [],
        public ?ResourcePatch $resources = null,
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

        if ($this->fragments !== []) {
            $payload['fragments'] = array_map(
                static fn (Fragment $fragment): array => $fragment->jsonSerialize(),
                $this->fragments,
            );
        }

        if ($this->resources instanceof ResourcePatch) {
            $payload['resources'] = $this->resources->jsonSerialize();
        }

        return $payload;
    }

    /** @return array<string, mixed> */
    public static function jsonSchema(): array
    {
        return ['type' => 'object', 'required' => ['success'],
            'properties' => ['success' => ['type' => 'boolean'],
                'notifications' => ['type' => 'array', 'items' => ['$ref' => '#/$defs/Notification']],
                'redirect' => ['type' => 'string'],
                'refreshBlocks' => ['type' => 'array', 'items' => ['type' => 'string']],
                'errors' => ['type' => 'object', 'additionalProperties' => ['oneOf' => [['type' => 'string'], ['type' => 'array', 'items' => ['type' => 'string']]]]],
                'fragments' => ['type' => 'array', 'items' => ['$ref' => '#/$defs/Fragment']],
                'resources' => ['$ref' => '#/$defs/ResourcePatch']],
            'additionalProperties' => false];
    }
}

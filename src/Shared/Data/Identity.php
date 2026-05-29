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

/**
 * Authenticated user identity for the page shell.
 *
 * All fields are raw `string` data (not labels) — never resolved as i18n.
 *
 * @api
 */
final readonly class Identity implements JsonSerializable
{
    /**
     * @param string[] $roles
     */
    public function __construct(
        public string $id,
        public string $name,
        public ?string $email = null,
        public ?string $avatarUrl = null,
        public array $roles = [],
    ) {}

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        $payload = [
            'id' => $this->id,
            'name' => $this->name,
        ];

        if ($this->email !== null) {
            $payload['email'] = $this->email;
        }

        if ($this->avatarUrl !== null) {
            $payload['avatarUrl'] = $this->avatarUrl;
        }

        if ($this->roles !== []) {
            $payload['roles'] = $this->roles;
        }

        return $payload;
    }

    /** @return array<string, mixed> */
    public static function jsonSchema(): array
    {
        return ['type' => 'object', 'required' => ['id', 'name'],
            'properties' => ['id' => ['type' => 'string'], 'name' => ['type' => 'string'], 'email' => ['type' => 'string'], 'avatarUrl' => ['type' => 'string'], 'roles' => ['type' => 'array', 'items' => ['type' => 'string']]],
            'additionalProperties' => false];
    }
}

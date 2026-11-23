<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Shared\ValueObject;

use InvalidArgumentException;
use JsonSerializable;
use Middag\Ui\Shared\Concerns\AssertsStringList;

/**
 * The authenticated user as seen by the client shell: the identity fields, the
 * capability tokens the user holds and an optional logout URL.
 *
 * The wire shape is flat: the {@see Identity} keys, then `capabilities`
 * (always emitted) and `logoutUrl` (only when set).
 *
 * @api
 */
final readonly class SharedAuth implements JsonSerializable
{
    use AssertsStringList;

    /**
     * @param list<string> $capabilities Opaque authorization tokens held by the user
     *
     * @throws InvalidArgumentException when `capabilities` is not a list of strings
     */
    public function __construct(
        public Identity $identity,
        public array $capabilities = [],
        public ?string $logoutUrl = null,
    ) {
        self::assertStringList($capabilities, 'SharedAuth::$capabilities');
    }

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        $payload = [
            ...$this->identity->jsonSerialize(),
            'capabilities' => $this->capabilities,
        ];

        if ($this->logoutUrl !== null) {
            $payload['logoutUrl'] = $this->logoutUrl;
        }

        return $payload;
    }

    /** @return array<string, mixed> */
    public static function jsonSchema(): array
    {
        return ['type' => 'object', 'required' => ['id', 'name', 'capabilities'],
            'properties' => [
                'id' => ['type' => 'string'],
                'name' => ['type' => 'string'],
                'email' => ['type' => 'string'],
                'avatarUrl' => ['type' => 'string'],
                'roles' => ['type' => 'array', 'items' => ['type' => 'string']],
                'capabilities' => ['type' => 'array', 'items' => ['type' => 'string']],
                'logoutUrl' => ['type' => 'string'],
            ],
            'additionalProperties' => false];
    }
}

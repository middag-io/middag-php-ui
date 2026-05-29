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
use Middag\Ui\Shared\Enum\ActionTargetKind;
use Middag\Ui\Shared\Enum\HttpMethod;

/**
 * Where an {@see Action} goes when activated, discriminated by `kind`.
 *
 * - `link`    — a URL (`href`), optionally external.
 * - `route`   — a host-named route (`route` + `params`) resolved client-side.
 * - `request` — an HTTP mutation (`endpoint` + `method`).
 *
 * Build via the named constructors; the wire payload only carries the fields
 * relevant to the chosen kind.
 *
 * @api
 */
final readonly class ActionTarget implements JsonSerializable
{
    /**
     * @param array<string, mixed> $params
     */
    private function __construct(
        public ActionTargetKind $kind,
        public ?string $href = null,
        public bool $external = false,
        public ?string $route = null,
        public array $params = [],
        public ?string $endpoint = null,
        public ?HttpMethod $method = null,
    ) {}

    public static function link(string $href, bool $external = false): self
    {
        return new self(kind: ActionTargetKind::LINK, href: $href, external: $external);
    }

    /**
     * @param array<string, mixed> $params
     */
    public static function route(string $name, array $params = []): self
    {
        return new self(kind: ActionTargetKind::ROUTE, route: $name, params: $params);
    }

    public static function request(string $endpoint, HttpMethod $method = HttpMethod::POST): self
    {
        return new self(kind: ActionTargetKind::REQUEST, endpoint: $endpoint, method: $method);
    }

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        return match ($this->kind) {
            ActionTargetKind::LINK => array_merge(
                ['kind' => 'link', 'href' => $this->href],
                $this->external ? ['external' => true] : [],
            ),
            ActionTargetKind::ROUTE => array_merge(
                ['kind' => 'route', 'route' => $this->route],
                $this->params !== [] ? ['params' => $this->params] : [],
            ),
            ActionTargetKind::REQUEST => [
                'kind' => 'request',
                'endpoint' => $this->endpoint,
                'method' => $this->method?->value,
            ],
        };
    }

    /**
     * Discriminated union on `kind` — exactly mirrors the per-branch key sets
     * emitted by {@see self::jsonSerialize()}. Each branch is closed; only the
     * fields relevant to its kind appear.
     *
     * @return array<string, mixed>
     */
    public static function jsonSchema(): array
    {
        return [
            'oneOf' => [
                // link: {kind:'link', href} (+ external:true only when external).
                [
                    'type' => 'object',
                    'required' => ['kind', 'href'],
                    'properties' => [
                        'kind' => ['const' => 'link'],
                        'href' => ['type' => 'string'],
                        'external' => ['const' => true],
                    ],
                    'additionalProperties' => false,
                ],
                // route: {kind:'route', route} (+ params map, omit-empty).
                [
                    'type' => 'object',
                    'required' => ['kind', 'route'],
                    'properties' => [
                        'kind' => ['const' => 'route'],
                        'route' => ['type' => 'string'],
                        'params' => ['type' => 'object', 'additionalProperties' => true],
                    ],
                    'additionalProperties' => false,
                ],
                // request: {kind:'request', endpoint, method}.
                [
                    'type' => 'object',
                    'required' => ['kind', 'endpoint', 'method'],
                    'properties' => [
                        'kind' => ['const' => 'request'],
                        'endpoint' => ['type' => 'string'],
                        'method' => ['$ref' => '#/$defs/HttpMethod'],
                    ],
                    'additionalProperties' => false,
                ],
            ],
        ];
    }
}

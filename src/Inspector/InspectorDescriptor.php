<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Inspector;

use Middag\Ui\Inspector\Contract\InspectorDescriptorInterface;
use Middag\Ui\Region\PollConfig;

/**
 * Inspector side-panel descriptor: endpoint (with {id} placeholder), width,
 * and optional poll config.
 *
 * @api
 */
readonly class InspectorDescriptor implements InspectorDescriptorInterface
{
    public function __construct(
        public string $endpoint,
        public int $width = 440,
        public ?PollConfig $poll = null,
    ) {}

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        $payload = [
            'endpoint' => $this->endpoint,
            'width' => $this->width,
        ];

        if ($this->poll instanceof PollConfig) {
            $payload['poll'] = $this->poll->jsonSerialize();
        }

        return $payload;
    }

    /** @return array<string, mixed> */
    public static function jsonSchema(): array
    {
        return ['type' => 'object', 'required' => ['endpoint', 'width'],
            'properties' => ['endpoint' => ['type' => 'string'], 'width' => ['type' => 'integer'], 'poll' => ['$ref' => '#/$defs/PollConfig']],
            'additionalProperties' => false];
    }
}

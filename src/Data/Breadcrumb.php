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

use Middag\Ui\Contract\BreadcrumbInterface;

/**
 * A single breadcrumb trail entry: label plus optional href.
 *
 * @api
 */
readonly class Breadcrumb implements BreadcrumbInterface
{
    public function __construct(
        public string|Translatable $label,
        public ?string $href = null,
        public bool $external = false,
    ) {}

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        $payload = ['label' => Label::serialize($this->label)];

        if ($this->href !== null) {
            $payload['href'] = $this->href;
        }

        if ($this->external) {
            $payload['external'] = true;
        }

        return $payload;
    }

    /** @return array<string, mixed> */
    public static function jsonSchema(): array
    {
        return ['type' => 'object', 'required' => ['label'],
            'properties' => ['label' => ['$ref' => '#/$defs/Label'], 'href' => ['type' => 'string'], 'external' => ['const' => true]],
            'additionalProperties' => false];
    }
}

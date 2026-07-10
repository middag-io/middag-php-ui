<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Block;

use JsonSerializable;

/**
 * A single named data series within a chart block. `key` matches a field on each
 * chart data row; `label` is the human-facing series name; `color` is an optional
 * CSS color token (e.g. `var(--chart-1)`).
 *
 * @api
 */
final readonly class ChartSeries implements JsonSerializable
{
    public function __construct(
        public string $key,
        public string $label,
        public ?string $color = null,
    ) {}

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        $out = ['key' => $this->key, 'label' => $this->label];
        if ($this->color !== null) {
            $out['color'] = $this->color;
        }

        return $out;
    }

    /** @return array<string, mixed> */
    public static function jsonSchema(): array
    {
        return ['type' => 'object', 'required' => ['key', 'label'],
            'properties' => ['key' => ['type' => 'string'], 'label' => ['type' => 'string'], 'color' => ['type' => 'string']],
            'additionalProperties' => false];
    }
}

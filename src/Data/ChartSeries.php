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

/**
 * A single named data series within a chart block.
 *
 * @api
 */
final readonly class ChartSeries implements JsonSerializable
{
    /**
     * @param float[] $data
     */
    public function __construct(
        public string $name,
        public array $data = [],
    ) {}

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name,
            'data' => $this->data,
        ];
    }
}

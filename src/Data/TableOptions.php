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
 * General behavior options for a data table.
 *
 * @api
 */
final readonly class TableOptions implements JsonSerializable
{
    public function __construct(
        public int $perPage = 25,
        public ?string $sortColumn = null,
        public string $sortDirection = 'desc',
        public bool $selectable = false,
        public bool $searchable = false,
    ) {}

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        $payload = [
            'perPage' => $this->perPage,
            'sortDirection' => $this->sortDirection,
            'selectable' => $this->selectable,
            'searchable' => $this->searchable,
        ];

        if ($this->sortColumn !== null) {
            $payload['sortColumn'] = $this->sortColumn;
        }

        return $payload;
    }
}

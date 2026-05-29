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
 * Typed pagination state for a data table.
 *
 * @api
 */
final readonly class Pagination implements JsonSerializable
{
    public function __construct(
        public int $page,
        public int $perPage,
        public int $total,
        public int $lastPage,
    ) {}

    /**
     * Build pagination deriving `lastPage` from total / perPage.
     */
    public static function of(int $page, int $perPage, int $total): self
    {
        $lastPage = $perPage > 0 ? (int) max(1, ceil($total / $perPage)) : 1;

        return new self($page, $perPage, $total, $lastPage);
    }

    /** @return array<string, int> */
    public function jsonSerialize(): array
    {
        return [
            'page' => $this->page,
            'perPage' => $this->perPage,
            'total' => $this->total,
            'lastPage' => $this->lastPage,
        ];
    }

    /** @return array<string, mixed> */
    public static function jsonSchema(): array
    {
        return [
            'type' => 'object',
            'required' => ['page', 'perPage', 'total', 'lastPage'],
            'properties' => [
                'page' => ['type' => 'integer'],
                'perPage' => ['type' => 'integer'],
                'total' => ['type' => 'integer'],
                'lastPage' => ['type' => 'integer'],
            ],
            'additionalProperties' => false,
        ];
    }
}

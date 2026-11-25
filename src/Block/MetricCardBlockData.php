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

/**
 * The `data` of a `metric_card` block: a `value` and `label`, with an optional
 * delta and its direction, icon, link, locale and compact flag.
 *
 * A schema-only value shape (no instances): it types block data for schema
 * consumers. {@see BlockBuilder} keeps taking plain arrays.
 *
 * @api
 */
final class MetricCardBlockData
{
    private function __construct() {}

    /** @return array<string, mixed> */
    public static function jsonSchema(): array
    {
        return [
            'type' => 'object',
            'required' => ['value', 'label'],
            'properties' => [
                'value' => [
                    'type' => ['string', 'number'],
                ],
                'delta' => ['type' => 'string'],
                'deltaDirection' => [
                    'enum' => ['positive', 'negative', 'neutral'],
                ],
                'label' => ['type' => 'string'],
                'icon' => ['type' => 'string'],
                'href' => ['type' => 'string'],
                'locale' => ['type' => 'string'],
                'compact' => ['type' => 'boolean'],
            ],
            'additionalProperties' => false,
        ];
    }
}

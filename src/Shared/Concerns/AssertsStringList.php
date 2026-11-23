<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Shared\Concerns;

use InvalidArgumentException;

/**
 * Runtime guard for `list<string>` constructor arguments, which PHPDoc alone
 * does not enforce.
 *
 * @internal
 */
trait AssertsStringList
{
    /**
     * @param array<array-key, mixed> $values
     *
     * @throws InvalidArgumentException when `$values` is not a list of strings
     */
    private static function assertStringList(array $values, string $field): void
    {
        if (!array_is_list($values)) {
            throw new InvalidArgumentException(sprintf('%s must be a list.', $field));
        }

        foreach ($values as $value) {
            if (!is_string($value)) {
                throw new InvalidArgumentException(sprintf(
                    '%s must contain only strings, %s given.',
                    $field,
                    get_debug_type($value),
                ));
            }
        }
    }
}

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
use Middag\Ui\Shared\Enum\ThemeMode;
use stdClass;

/**
 * Theme slot of the shared props: the appearance mode and a map of
 * client string overrides.
 *
 * `strings` is always emitted and serializes to `{}` when empty, never `[]`.
 *
 * @api
 */
final readonly class SharedTheme implements JsonSerializable
{
    /**
     * @param array<string, string> $strings
     *
     * @throws InvalidArgumentException when a `strings` value is not a string
     */
    public function __construct(
        public ?ThemeMode $appearance = null,
        public array $strings = [],
    ) {
        $this->assertStringMap($strings, 'SharedTheme::$strings');
    }

    /** @return array{appearance?: string, strings: stdClass} */
    public function jsonSerialize(): array
    {
        $payload = [];

        if ($this->appearance instanceof ThemeMode) {
            $payload['appearance'] = $this->appearance->value;
        }

        $payload['strings'] = (object) $this->strings;

        return $payload;
    }

    /** @return array<string, mixed> */
    public static function jsonSchema(): array
    {
        return ['type' => 'object',
            'properties' => [
                'appearance' => ['$ref' => '#/$defs/ThemeMode'],
                'strings' => ['type' => 'object', 'additionalProperties' => ['type' => 'string']],
            ],
            'additionalProperties' => false];
    }

    /**
     * @param array<array-key, mixed> $values
     *
     * @throws InvalidArgumentException when a value is not a string
     */
    private function assertStringMap(array $values, string $field): void
    {
        foreach ($values as $key => $value) {
            if (!is_string($value)) {
                throw new InvalidArgumentException(sprintf(
                    '%s["%s"] must be a string, %s given.',
                    $field,
                    $key,
                    get_debug_type($value),
                ));
            }
        }
    }
}

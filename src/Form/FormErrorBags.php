<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Form;

use InvalidArgumentException;
use JsonSerializable;
use stdClass;

/**
 * Validation errors scoped to error bags: bag name → {@see FormErrors}.
 *
 * Used for the `errors` shared prop when the request named an error bag (the
 * `X-Inertia-Error-Bag` header), so several forms on one page keep their
 * errors apart. Always serializes as a JSON object, also when a bag name is
 * numeric (PHP turns the key `'0'` into the int `0`) or when there is no bag.
 *
 * @api
 */
final readonly class FormErrorBags implements JsonSerializable
{
    /**
     * @param array<int|string, FormErrors> $bags bag name → errors of that bag
     *
     * @throws InvalidArgumentException when a value is not a FormErrors or a bag name is empty
     */
    public function __construct(
        public array $bags = [],
    ) {
        $this->assertBags($bags);
    }

    public function jsonSerialize(): stdClass
    {
        return (object) array_map(
            static fn (FormErrors $errors): stdClass => $errors->jsonSerialize(),
            $this->bags,
        );
    }

    /** @return array<string, mixed> */
    public static function jsonSchema(): array
    {
        return [
            'type' => 'object',
            'description' => 'Validation errors scoped to error bags: bag name to FormErrors.',
            'additionalProperties' => ['$ref' => '#/$defs/FormErrors'],
        ];
    }

    /**
     * @param array<array-key, mixed> $bags
     */
    private function assertBags(array $bags): void
    {
        foreach ($bags as $name => $errors) {
            if ($name === '') {
                throw new InvalidArgumentException('FormErrorBags bag names must not be empty.');
            }

            if (!$errors instanceof FormErrors) {
                throw new InvalidArgumentException(sprintf(
                    'FormErrorBags bag "%s" must be a %s, %s given.',
                    $name,
                    FormErrors::class,
                    get_debug_type($errors),
                ));
            }
        }
    }
}

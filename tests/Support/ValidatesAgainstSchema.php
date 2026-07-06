<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Tests\Support;

use JsonSerializable;
use Middag\Ui\Schema\SchemaRegistry;
use Middag\Ui\Tests\Schema\FormSchemaNodeTest;
use Opis\JsonSchema\Errors\ErrorFormatter;
use Opis\JsonSchema\Errors\ValidationError;
use Opis\JsonSchema\Validator;

/**
 * Validate a serialized value (or raw payload) against one registered `$def`,
 * with the full {@see SchemaRegistry} bundle available so internal `$ref`s
 * resolve. Mirrors the helper proven in {@see FormSchemaNodeTest}
 * so every schema-only wire VO can assert its `jsonSchema()` accepts the shapes
 * it must and rejects the ones it must not (the over-loosening regression class).
 *
 * @internal
 */
trait ValidatesAgainstSchema
{
    /**
     * Assert a value is ACCEPTED by a registered `$def`.
     *
     * @param array<string, mixed>|bool|float|int|JsonSerializable|list<mixed>|object|string $value
     */
    protected static function assertValidAgainst(string $defName, mixed $value): void
    {
        $serialized = $value instanceof JsonSerializable ? $value->jsonSerialize() : $value;
        $result = (new Validator())->validate(self::asData($serialized), self::schemaFor($defName));

        if (!$result->isValid()) {
            $error = $result->error();
            $formatted = $error instanceof ValidationError ? (new ErrorFormatter())->format($error) : [];
            self::fail($defName . ' rejected a payload it must accept:' . "\n"
                . json_encode($formatted, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }

        self::assertTrue($result->isValid());
    }

    /**
     * Assert a raw payload is REJECTED by a registered `$def`.
     *
     * @param array<string, mixed>|bool|float|int|list<mixed>|string $value
     */
    protected static function assertInvalidAgainst(string $defName, mixed $value): void
    {
        $result = (new Validator())->validate(self::asData($value), self::schemaFor($defName));

        self::assertFalse(
            $result->isValid(),
            $defName . ' must reject an invalid payload (branch strictness over-loosened?).',
        );
    }

    private static function asData(mixed $value): mixed
    {
        return json_decode(json_encode($value) ?: 'null');
    }

    private static function schemaFor(string $defName): mixed
    {
        return json_decode(json_encode([
            '$ref' => '#/$defs/' . $defName,
            '$defs' => SchemaRegistry::defs(),
        ]) ?: 'null');
    }
}

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

use Middag\Ui\Schema\SchemaRegistry;
use Opis\JsonSchema\Errors\ErrorFormatter;
use Opis\JsonSchema\Errors\ValidationError;
use Opis\JsonSchema\Validator;

/**
 * Every leaf error a payload raises against one registered `$def`, as
 * `<pointer> [<keyword>] <message>` lines in validation order.
 *
 * The golden fixtures record this list for a divergent payload, so a schema
 * change that alters why a payload is rejected shows up as a stale record.
 *
 * @internal
 */
final class SchemaErrors
{
    private const int MAX_ERRORS = 100;

    /**
     * @return list<string> empty when the payload is valid
     */
    public static function of(string $defName, mixed $data): array
    {
        $schema = json_decode(json_encode([
            '$ref' => '#/$defs/' . $defName,
            '$defs' => SchemaRegistry::defs(),
        ]) ?: 'null');
        $result = (new Validator())->setMaxErrors(self::MAX_ERRORS)->validate($data, $schema);
        $error = $result->error();

        return $error instanceof ValidationError ? self::leaves($error, new ErrorFormatter()) : [];
    }

    /**
     * @return list<string>
     */
    private static function leaves(ValidationError $error, ErrorFormatter $formatter): array
    {
        if ($error->subErrors() === []) {
            $pointer = '/' . implode('/', array_map(strval(...), $error->data()->fullPath()));

            return [$pointer . ' [' . $error->keyword() . '] ' . $formatter->formatErrorMessage($error)];
        }

        $lines = [];
        foreach ($error->subErrors() as $sub) {
            $lines = [...$lines, ...self::leaves($sub, $formatter)];
        }

        return $lines;
    }
}

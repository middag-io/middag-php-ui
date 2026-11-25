<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Tests\Block;

use Middag\Ui\Schema\SchemaRegistry;
use Middag\Ui\Tests\Support\SchemaErrors;
use Middag\Ui\Tests\Support\ValidatesAgainstSchema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Golden fixtures: block `data` payloads that real producers emit today,
 * validated against the block data `$defs`.
 *
 * Each fixture under `tests/Fixtures/BlockData/` names the `$def` it is checked
 * against and what to expect. `valid` means the schema accepts the payload.
 * `divergent` records a payload the schema does not accept on purpose, with
 * the exact errors it raises in `divergence` (see {@see SchemaErrors}): the
 * test asserts the validator still reports exactly those errors, so a schema
 * change that accepts the payload, or rejects it for another reason, has to
 * update the record.
 *
 * @internal
 */
#[CoversClass(SchemaRegistry::class)]
final class BlockDataFixtureTest extends TestCase
{
    use ValidatesAgainstSchema;

    private const string FIXTURES = __DIR__ . '/../Fixtures/BlockData';

    #[Test]
    #[DataProvider('fixtureProvider')]
    /**
     * @param list<string> $divergence
     */
    public function fixtureMatchesItsExpectation(string $def, string $expect, object $data, array $divergence): void
    {
        self::assertArrayHasKey($def, SchemaRegistry::defs(), $def . ' is not a registered $def.');

        if ($expect === 'divergent') {
            self::assertNotSame([], $divergence, 'A divergent fixture must record its errors.');
            self::assertSame($divergence, SchemaErrors::of($def, $data), 'The recorded divergence is stale.');

            return;
        }

        self::assertSame('valid', $expect);
        self::assertSame([], $divergence, 'A valid fixture records no divergence.');
        self::assertValidAgainst($def, $data);
    }

    /**
     * @return array<string, array{string, string, object, list<string>}>
     */
    public static function fixtureProvider(): array
    {
        $cases = [];
        $files = glob(self::FIXTURES . '/*/*.json') ?: [];
        sort($files);

        foreach ($files as $file) {
            /** @var object{def: string, expect: string, data: object, divergence?: list<string>} $fixture */
            $fixture = json_decode((string) file_get_contents($file), false, 512, JSON_THROW_ON_ERROR);
            $cases[basename(dirname($file)) . '/' . basename($file)] = [$fixture->def, $fixture->expect, $fixture->data, $fixture->divergence ?? []];
        }

        return $cases;
    }

    #[Test]
    public function schemaErrorsListsEachLeafErrorWithPointerAndKeyword(): void
    {
        self::assertSame(
            ['/cta [required] The required properties (href) are missing'],
            SchemaErrors::of('EmptyStateDef', json_decode('{"cta": {"label": "Go"}}')),
        );
        self::assertSame(
            ['/ [additionalProperties] Additional object properties are not allowed: extra'],
            SchemaErrors::of('EmptyStateDef', json_decode('{"extra": 1}')),
        );
        self::assertSame([], SchemaErrors::of('EmptyStateDef', json_decode('{}')));
    }

    #[Test]
    public function fixturesExist(): void
    {
        self::assertNotSame([], self::fixtureProvider());
    }
}

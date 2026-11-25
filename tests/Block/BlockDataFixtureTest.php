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
 * `divergent` records a payload the schema does not accept on purpose (the
 * fixture says why): the test asserts it is still rejected, so a later change
 * that starts accepting it has to update the fixture.
 *
 * @internal
 */
#[CoversClass(SchemaRegistry::class)]
class BlockDataFixtureTest extends TestCase
{
    use ValidatesAgainstSchema;

    private const string FIXTURES = __DIR__ . '/../Fixtures/BlockData';

    #[Test]
    #[DataProvider('fixtureProvider')]
    public function fixtureMatchesItsExpectation(string $def, string $expect, object $data): void
    {
        self::assertArrayHasKey($def, SchemaRegistry::defs(), $def . ' is not a registered $def.');

        if ($expect === 'divergent') {
            self::assertInvalidAgainst($def, $data);

            return;
        }

        self::assertSame('valid', $expect);
        self::assertValidAgainst($def, $data);
    }

    /**
     * @return array<string, array{string, string, object}>
     */
    public static function fixtureProvider(): array
    {
        $cases = [];
        $files = glob(self::FIXTURES . '/*/*.json') ?: [];
        sort($files);

        foreach ($files as $file) {
            /** @var object{def: string, expect: string, data: object} $fixture */
            $fixture = json_decode((string) file_get_contents($file), false, 512, JSON_THROW_ON_ERROR);
            $cases[basename(dirname($file)) . '/' . basename($file)] = [$fixture->def, $fixture->expect, $fixture->data];
        }

        return $cases;
    }

    #[Test]
    public function fixturesExist(): void
    {
        self::assertNotSame([], self::fixtureProvider());
    }
}

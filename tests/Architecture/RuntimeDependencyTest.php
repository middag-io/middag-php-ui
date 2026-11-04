<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Tests\Architecture;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Architecture guard (UI-001): the package requires only PHP at runtime, so
 * consumers inherit no transitive dependency. Any `require` entry other than
 * `php` fails this test; tooling belongs in `require-dev`.
 *
 * @internal
 */
#[CoversNothing]
final class RuntimeDependencyTest extends TestCase
{
    private const COMPOSER_JSON = __DIR__ . '/../../composer.json';

    #[Test]
    public function composerRequireHoldsOnlyPhp(): void
    {
        $contents = file_get_contents(self::COMPOSER_JSON);
        $this->assertIsString($contents, 'composer.json must be readable.');

        $manifest = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
        $this->assertIsArray($manifest);

        $require = $manifest['require'] ?? [];
        $this->assertIsArray($require);

        $this->assertSame(
            ['php'],
            array_keys($require),
            'middag-io/ui must require only PHP at runtime (UI-001); move tooling to require-dev.'
        );
    }
}

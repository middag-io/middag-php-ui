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
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

/**
 * API-STABILITY.md promises that every type in src/ carries exactly one of
 * `@api` or `@internal`, and that `Schema/` and `Shared/Concerns/ProvidesJsonSchema`
 * are `@internal`. A type with both tags, with neither, or an excluded type
 * tagged `@api` fails here.
 *
 * @internal
 */
#[CoversNothing]
final class ApiStabilityTagTest extends TestCase
{
    private const string DECLARATION = '~/\*\*((?:(?!\*/).)*?)\*/\s*(?:#\[[^\]]*\]\s*)*(?:(?:final|abstract|readonly)\s+)*(?:class|interface|trait|enum)\s+(\w+)~s';

    /**
     * Types that API-STABILITY.md declares `@internal`, as path prefixes under src/.
     */
    private const array INTERNAL_PREFIXES = [
        'Schema/',
        'Shared/Concerns/ProvidesJsonSchema.php',
    ];

    #[Test]
    public function everyTypeInSrcCarriesExactlyOneStabilityTag(): void
    {
        $types = $this->typesInSrc();
        self::assertNotEmpty($types, 'no type found in src/: the scan is broken');

        $violations = [];
        foreach ($types as $type) {
            $api = 1 === preg_match('/@api\b/', $type['doc']);
            $internal = 1 === preg_match('/@internal\b/', $type['doc']);

            if ($api === $internal) {
                $violations[] = $type['name'] . ($api ? ' has both @api and @internal' : ' has neither @api nor @internal');
            }
        }

        self::assertSame([], $violations, 'every type in src/ must carry exactly one of @api / @internal');
    }

    #[Test]
    public function typesDeclaredInternalInApiStabilityAreNotTaggedApi(): void
    {
        $leaks = [];
        foreach ($this->typesInSrc() as $type) {
            foreach (self::INTERNAL_PREFIXES as $prefix) {
                if (str_starts_with($type['path'], $prefix) && 1 === preg_match('/@api\b/', $type['doc'])) {
                    $leaks[] = $type['name'];
                }
            }
        }

        self::assertSame([], $leaks, 'declared @internal in API-STABILITY.md but tagged @api');
    }

    /**
     * @return list<array{name: string, path: string, doc: string}>
     */
    private function typesInSrc(): array
    {
        $src = dirname(__DIR__, 2) . '/src';
        $types = [];
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($src, RecursiveDirectoryIterator::SKIP_DOTS));

        /** @var SplFileInfo $file */
        foreach ($files as $file) {
            if ('php' !== $file->getExtension()) {
                continue;
            }

            $code = (string) file_get_contents($file->getPathname());
            if (1 !== preg_match('/^namespace ([^;]+);/m', $code, $namespace)) {
                continue;
            }

            preg_match_all(self::DECLARATION, $code, $matches, PREG_SET_ORDER);
            foreach ($matches as $match) {
                $types[] = [
                    'name' => $namespace[1] . '\\' . $match[2],
                    'path' => substr($file->getPathname(), strlen($src) + 1),
                    'doc' => $match[1],
                ];
            }
        }

        return $types;
    }
}

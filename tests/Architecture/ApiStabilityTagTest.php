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

use PhpToken;
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

            foreach ($this->declarationsIn($code) as $declaration) {
                $types[] = [
                    'name' => $namespace[1] . '\\' . $declaration['name'],
                    'path' => substr($file->getPathname(), strlen($src) + 1),
                    'doc' => $declaration['doc'],
                ];
            }
        }

        return $types;
    }

    /**
     * Finds every class-like declaration with the PHP tokenizer, so a type
     * without a docblock is still found (its doc is then the empty string).
     *
     * @return list<array{name: string, doc: string}>
     */
    private function declarationsIn(string $code): array
    {
        $tokens = array_values(array_filter(
            PhpToken::tokenize($code),
            static fn (PhpToken $token): bool => !$token->is([T_WHITESPACE, T_COMMENT]),
        ));
        $declarations = [];

        foreach ($tokens as $index => $token) {
            if (!$token->is([T_CLASS, T_INTERFACE, T_TRAIT, T_ENUM])) {
                continue;
            }

            $previous = $tokens[$index - 1] ?? null;
            $next = $tokens[$index + 1] ?? null;
            // Skip Foo::class, new class {...} and anonymous classes.
            if ($previous?->is([T_DOUBLE_COLON, T_NEW]) || !$next?->is(T_STRING)) {
                continue;
            }

            $declarations[] = ['name' => $next->text, 'doc' => $this->docBefore($tokens, $index)];
        }

        return $declarations;
    }

    /**
     * Returns the docblock directly before a declaration, skipping modifiers
     * and attributes, or '' when there is none.
     *
     * @param list<PhpToken> $tokens
     */
    private function docBefore(array $tokens, int $declaration): string
    {
        $cursor = $declaration - 1;
        while ($cursor >= 0) {
            $token = $tokens[$cursor];
            if ($token->is([T_FINAL, T_ABSTRACT, T_READONLY])) {
                --$cursor;

                continue;
            }

            if ($token->is(']')) {
                $depth = 0;
                for (; $cursor >= 0; --$cursor) {
                    if ($tokens[$cursor]->is('[')) {
                        ++$depth;
                    } elseif ($tokens[$cursor]->is(']')) {
                        --$depth;
                    } elseif ($tokens[$cursor]->is(T_ATTRIBUTE)) {
                        // T_ATTRIBUTE "#[" opens one bracket that no "[" token matched.
                        ++$depth;
                    }

                    if (0 === $depth) {
                        break;
                    }
                }
                --$cursor;

                continue;
            }

            return $token->is(T_DOC_COMMENT) ? $token->text : '';
        }

        return '';
    }
}

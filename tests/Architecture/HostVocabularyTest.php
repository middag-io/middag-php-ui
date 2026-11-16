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
 * Architecture guard (UI-001): ui never knows the host. `src/` and `schema/`
 * carry no host vocabulary, in code, docblocks, comments or schema text: no
 * host name, host form engine, host database or config global, host capability
 * or login call, host file-area field or host plugin convention.
 *
 * The word `html` and `RenderTarget::Html` are not host vocabulary: ui may
 * carry a markup body produced downstream as opaque data. The guard fails
 * closed: a root with no file to scan fails instead of passing.
 *
 * @internal
 */
#[CoversNothing]
final class HostVocabularyTest extends TestCase
{
    private const string ROOT = __DIR__ . '/../..';

    /** Scanned roots, relative to the package root, and the file extension scanned in each. */
    private const array ROOTS = [
        'src' => 'php',
        'schema' => 'json',
    ];

    /**
     * Host vocabulary, as PCRE patterns. Each one names a host, a host API or
     * a host convention; none matches a neutral word of the contracts.
     */
    private const array DENY = [
        'host name' => '/\b(moodle|wordpress)\b/i',
        'host form engine' => '/\b(mform|moodleform|quickform)\b/i',
        'host database' => '/\bwpdb\b/i',
        'host global' => '/\$(DB|CFG|USER|PAGE|OUTPUT|SITE|COURSE|SESSION)\b/',
        'host capability or login call' => '/\b(has_capability|require_capability|require_login|current_user_can|user_can)\b/i',
        'host string or config call' => '/\b(get_string|get_config|set_config)\s*\(/i',
        'host hook call' => '/\b(add_action|add_filter|do_action|apply_filters)\b/i',
        'host function prefix' => '/\bwp_[a-z_]+/i',
        'host session key' => '/\bsesskey\b/i',
        'host file-area field' => '/\b(draft_?item_?id|draft_?area|file_?area|pluginfile|itemid)\b/i',
        'host context class' => '/\bcontext_(system|course|module|user|block|coursecat)\b/i',
        'host plugin convention' => '/\b(frankenstyle|version\.php|lib\.php|db\/access\.php)\b/i',
    ];

    #[Test]
    public function sourceAndSchemaCarryNoHostVocabulary(): void
    {
        $violations = [];

        foreach (self::ROOTS as $root => $extension) {
            $files = $this->filesUnder($root, $extension);
            self::assertNotSame([], $files, sprintf('No .%s file found under %s/: the guard must scan something.', $extension, $root));

            foreach ($files as $file) {
                $violations = [...$violations, ...$this->violationsIn($file)];
            }
        }

        self::assertSame([], $violations, "Host vocabulary in ui (UI-001); move it to the host adapter:\n" . implode("\n", $violations));
    }

    #[Test]
    public function denyListMatchesKnownHostVocabulary(): void
    {
        $samples = [
            'carries the Moodle draft area id',
            'public ?int $draftitemid',
            '"draftItemId": 1',
            'global $DB;',
            'has_capability(\'x\', $ctx)',
            'wp_enqueue_script',
            'new moodleform()',
            "add_filter('the_content')",
        ];

        foreach ($samples as $sample) {
            self::assertTrue($this->matchesDenyList($sample), 'The deny list must catch: ' . $sample);
        }
    }

    #[Test]
    public function denyListAllowsNeutralWords(): void
    {
        $samples = [
            'RenderTarget::Html',
            'Build an HTML-body output.',
            'https://json-schema.org/draft/2020-12/schema',
            'public ?string $capability',
            'public ?string $uploadToken',
            'a pending upload the host round-trips',
        ];

        foreach ($samples as $sample) {
            self::assertFalse($this->matchesDenyList($sample), 'The deny list must not flag: ' . $sample);
        }
    }

    /** @return list<string> */
    private function filesUnder(string $root, string $extension): array
    {
        $directory = self::ROOT . '/' . $root;
        if (!is_dir($directory)) {
            return [];
        }

        $files = [];
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS));

        /** @var SplFileInfo $file */
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === $extension) {
                $files[] = $file->getPathname();
            }
        }

        sort($files);

        return $files;
    }

    /** @return list<string> */
    private function violationsIn(string $file): array
    {
        $lines = file($file, FILE_IGNORE_NEW_LINES);
        self::assertIsArray($lines, $file . ' must be readable.');

        $relative = substr($file, strlen(self::ROOT) + 1);
        $violations = [];

        foreach ($lines as $index => $line) {
            foreach (self::DENY as $label => $pattern) {
                if (preg_match($pattern, $line, $match) === 1) {
                    $violations[] = sprintf('%s:%d: %s (%s)', $relative, $index + 1, $match[0], $label);
                }
            }
        }

        return $violations;
    }

    private function matchesDenyList(string $text): bool
    {
        foreach (self::DENY as $pattern) {
            if (preg_match($pattern, $text) === 1) {
                return true;
            }
        }

        return false;
    }
}

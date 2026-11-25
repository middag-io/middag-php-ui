<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Tests\Page;

use Middag\Ui\Page\PageBuilder;
use Middag\Ui\Page\PageContract;
use Middag\Ui\Tests\Support\ValidatesAgainstSchema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Where `help` and `inspector` go: `build()` keeps them in the PageContract,
 * and `toProps()` still carries them as sibling props.
 *
 * @internal
 */
#[CoversClass(PageBuilder::class)]
#[CoversClass(PageContract::class)]
final class PageBuilderSidePropsTest extends TestCase
{
    use ValidatesAgainstSchema;

    #[Test]
    public function buildKeepsHelpAndInspectorInTheContract(): void
    {
        $builder = PageBuilder::page('p')->help('Title', 'Body', 'https://example.test/docs')->inspector('/inspect/{id}', 500);

        $wire = json_decode(json_encode($builder->build()) ?: 'null', true);

        self::assertIsArray($wire);
        self::assertSame(['title' => 'Title', 'body' => 'Body', 'learnMore' => 'https://example.test/docs'], $wire['help'] ?? null);
        self::assertSame(['endpoint' => '/inspect/{id}', 'width' => 500], $wire['inspector'] ?? null);
        self::assertValidAgainst('PageContract', $builder->build());
    }

    #[Test]
    public function buildOmitsANullLearnMoreAndUnsetSlots(): void
    {
        $withHelp = json_decode(json_encode(PageBuilder::page('p')->help('T', 'B')->build()) ?: 'null', true);
        $bare = json_decode(json_encode(PageBuilder::page('p')->build()) ?: 'null', true);

        self::assertIsArray($withHelp);
        self::assertSame(['title' => 'T', 'body' => 'B'], $withHelp['help'] ?? null);
        self::assertIsArray($bare);
        self::assertArrayNotHasKey('help', $bare);
        self::assertArrayNotHasKey('inspector', $bare);
    }

    #[Test]
    public function toPropsCarriesHelpAndInspector(): void
    {
        $props = PageBuilder::page('p')->help('Title', 'Body')->inspector('/inspect/{id}')->toProps();

        self::assertSame(['title' => 'Title', 'body' => 'Body', 'learnMore' => null], $props['help'] ?? null);
        self::assertSame('/inspect/{id}', ($props['inspector'] ?? null)?->endpoint);
    }
}

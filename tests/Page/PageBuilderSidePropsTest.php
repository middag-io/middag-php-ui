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
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Where `help` and `inspector` go: `build()` returns the bare PageContract,
 * which has no slot for them, and `toProps()` carries them as sibling props.
 *
 * @internal
 */
#[CoversClass(PageBuilder::class)]
class PageBuilderSidePropsTest extends TestCase
{
    #[Test]
    public function buildLeavesHelpAndInspectorOutOfTheContract(): void
    {
        $builder = PageBuilder::page('p')->help('Title', 'Body')->inspector('/inspect/{id}');

        $wire = json_decode(json_encode($builder->build()) ?: 'null', true);

        self::assertIsArray($wire);
        self::assertArrayNotHasKey('help', $wire);
        self::assertArrayNotHasKey('inspector', $wire);
    }

    #[Test]
    public function toPropsCarriesHelpAndInspector(): void
    {
        $props = PageBuilder::page('p')->help('Title', 'Body')->inspector('/inspect/{id}')->toProps();

        self::assertSame('Title', $props['help']['title'] ?? null);
        self::assertSame('/inspect/{id}', ($props['inspector'] ?? null)?->endpoint);
    }
}

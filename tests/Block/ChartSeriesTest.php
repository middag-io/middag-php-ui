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

use Middag\Ui\Block\ChartSeries;
use Middag\Ui\Tests\Support\ValidatesAgainstSchema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @internal
 */
#[CoversClass(ChartSeries::class)]
final class ChartSeriesTest extends TestCase
{
    use ValidatesAgainstSchema;

    #[Test]
    public function testIsReadonlyClass(): void
    {
        self::assertTrue((new ReflectionClass(ChartSeries::class))->isReadOnly());
    }

    #[Test]
    public function testSerializesKeyAndLabel(): void
    {
        $series = new ChartSeries('revenue', 'Revenue');

        self::assertSame(
            ['key' => 'revenue', 'label' => 'Revenue'],
            $series->jsonSerialize(),
        );
    }

    #[Test]
    public function testSerializesColorWhenSet(): void
    {
        $series = new ChartSeries('revenue', 'Revenue', 'var(--chart-1)');

        self::assertSame(
            ['key' => 'revenue', 'label' => 'Revenue', 'color' => 'var(--chart-1)'],
            $series->jsonSerialize(),
        );
    }

    #[Test]
    public function testOmitsColorWhenNull(): void
    {
        self::assertArrayNotHasKey('color', (new ChartSeries('x', 'X'))->jsonSerialize());
    }

    #[Test]
    public function testSchemaAcceptsASeries(): void
    {
        $this->assertValidAgainst('ChartSeries', new ChartSeries('revenue', 'Revenue', 'var(--chart-1)'));
    }
}

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
    #[Test]
    public function testIsReadonlyClass(): void
    {
        self::assertTrue((new ReflectionClass(ChartSeries::class))->isReadOnly());
    }

    #[Test]
    public function testSerializesNameAndData(): void
    {
        $series = new ChartSeries('Revenue', [1.0, 2.5, 3.0]);

        self::assertSame(
            ['name' => 'Revenue', 'data' => [1.0, 2.5, 3.0]],
            $series->jsonSerialize(),
        );
    }

    #[Test]
    public function testDataDefaultsToEmpty(): void
    {
        $series = new ChartSeries('Empty');

        self::assertSame(['name' => 'Empty', 'data' => []], $series->jsonSerialize());
    }
}

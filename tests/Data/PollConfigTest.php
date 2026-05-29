<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Tests\Data;

use Middag\Ui\Data\PollConfig;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @internal
 */
#[CoversClass(PollConfig::class)]
final class PollConfigTest extends TestCase
{
    #[Test]
    public function testIsReadonlyClass(): void
    {
        self::assertTrue((new ReflectionClass(PollConfig::class))->isReadOnly());
    }

    #[Test]
    public function testSerializesMinimal(): void
    {
        $payload = (new PollConfig(endpoint: '/poll', intervalMs: 3000))->jsonSerialize();

        self::assertSame([
            'endpoint' => '/poll',
            'intervalMs' => 3000,
            'pauseWhenHidden' => true,
        ], $payload);
        self::assertArrayNotHasKey('stopAfterMs', $payload);
    }

    #[Test]
    public function testSerializesStopAfterWhenSet(): void
    {
        $payload = (new PollConfig(endpoint: '/poll', intervalMs: 3000, stopAfterMs: 60000, pauseWhenHidden: false))->jsonSerialize();

        self::assertFalse($payload['pauseWhenHidden']);
        self::assertSame(60000, $payload['stopAfterMs']);
    }
}

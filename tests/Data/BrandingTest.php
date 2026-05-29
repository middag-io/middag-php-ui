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

use Middag\Ui\Data\Branding;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @internal
 */
#[CoversClass(Branding::class)]
final class BrandingTest extends TestCase
{
    #[Test]
    public function testIsReadonlyClass(): void
    {
        self::assertTrue((new ReflectionClass(Branding::class))->isReadOnly());
    }

    #[Test]
    public function testSerializesMinimal(): void
    {
        $payload = (new Branding(appName: 'Helico'))->jsonSerialize();

        self::assertSame(['appName' => 'Helico'], $payload);
    }

    #[Test]
    public function testSerializesLogos(): void
    {
        $payload = (new Branding(
            appName: 'Helico',
            logoUrl: 'https://x/logo.svg',
            logoCompactUrl: 'https://x/logo-c.svg',
        ))->jsonSerialize();

        self::assertSame('https://x/logo.svg', $payload['logoUrl']);
        self::assertSame('https://x/logo-c.svg', $payload['logoCompactUrl']);
    }
}

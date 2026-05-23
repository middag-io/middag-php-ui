<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.com.br>
 * @copyright   2026 MIDDAG (https://www.middag.com.br)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Tests\Data;

use Middag\Ui\Data\PageResources;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(PageResources::class)]
final class PageResourcesTest extends TestCase
{
    #[Test]
    public function testSerializesDefaults(): void
    {
        $resources = new PageResources();

        $payload = $resources->jsonSerialize();

        self::assertArrayHasKey('auth', $payload);
        self::assertArrayHasKey('capabilities', $payload);
        self::assertArrayHasKey('featureFlags', $payload);
        self::assertArrayHasKey('locale', $payload);
        self::assertSame([], $payload['auth']);
        self::assertSame([], $payload['capabilities']);
        self::assertSame([], $payload['featureFlags']);
        self::assertSame('pt-BR', $payload['locale']);
    }

    #[Test]
    public function testSerializesCustomValues(): void
    {
        $resources = new PageResources(
            auth: ['id' => 1, 'name' => 'Admin'],
            capabilities: ['manage_users' => true],
            feature_flags: ['dark_mode' => true, 'beta' => false],
            locale: 'en',
        );

        $payload = $resources->jsonSerialize();

        self::assertSame(['id' => 1, 'name' => 'Admin'], $payload['auth']);
        self::assertSame(['manage_users' => true], $payload['capabilities']);
        self::assertSame(['dark_mode' => true, 'beta' => false], $payload['featureFlags']);
        self::assertSame('en', $payload['locale']);
    }

    #[Test]
    public function testFeatureFlagsCamelCase(): void
    {
        $resources = new PageResources(
            feature_flags: ['new_dashboard' => true],
        );

        $payload = $resources->jsonSerialize();

        self::assertArrayHasKey('featureFlags', $payload);
        self::assertArrayNotHasKey('feature_flags', $payload);
        self::assertSame(['new_dashboard' => true], $payload['featureFlags']);
    }
}

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

use Middag\Ui\Page\Branding;
use Middag\Ui\Page\PageResources;
use Middag\Ui\Shared\Enum\ThemeMode;
use Middag\Ui\Shared\ValueObject\Identity;
use Middag\Ui\Shared\ValueObject\UserPreferences;
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

        self::assertArrayHasKey('preferences', $payload);
        self::assertArrayHasKey('capabilities', $payload);
        self::assertArrayHasKey('featureFlags', $payload);
        self::assertArrayNotHasKey('user', $payload);
        self::assertArrayNotHasKey('branding', $payload);
        self::assertSame([], $payload['capabilities']);
        self::assertSame([], $payload['featureFlags']);
        self::assertSame('en', $payload['preferences']['locale']);
        self::assertSame('system', $payload['preferences']['theme']);
    }

    #[Test]
    public function testSerializesCustomValues(): void
    {
        $resources = new PageResources(
            preferences: new UserPreferences(theme: ThemeMode::DARK, locale: 'pt-BR'),
            capabilities: ['manage_users' => true],
            featureFlags: ['dark_mode' => true, 'beta' => false],
            user: new Identity(id: '1', name: 'Admin'),
            branding: new Branding(appName: 'Helico'),
        );

        $payload = $resources->jsonSerialize();

        self::assertSame('dark', $payload['preferences']['theme']);
        self::assertSame('pt-BR', $payload['preferences']['locale']);
        self::assertSame(['manage_users' => true], $payload['capabilities']);
        self::assertSame(['dark_mode' => true, 'beta' => false], $payload['featureFlags']);
        self::assertSame('Admin', $payload['user']['name']);
        self::assertSame('Helico', $payload['branding']['appName']);
    }

    #[Test]
    public function testFeatureFlagsCamelCase(): void
    {
        $resources = new PageResources(
            featureFlags: ['new_dashboard' => true],
        );

        $payload = $resources->jsonSerialize();

        self::assertArrayHasKey('featureFlags', $payload);
        self::assertArrayNotHasKey('feature_flags', $payload);
        self::assertSame(['new_dashboard' => true], $payload['featureFlags']);
    }
}

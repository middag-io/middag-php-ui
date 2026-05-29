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

use Middag\Ui\Page\ResourcePatch;
use Middag\Ui\Shared\Data\UserPreferences;
use Middag\Ui\Shared\Enum\ThemeMode;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @internal
 */
#[CoversClass(ResourcePatch::class)]
final class ResourcePatchTest extends TestCase
{
    #[Test]
    public function testIsReadonlyClass(): void
    {
        self::assertTrue((new ReflectionClass(ResourcePatch::class))->isReadOnly());
    }

    #[Test]
    public function testEmptyPatchSerializesToEmptyArray(): void
    {
        self::assertSame([], (new ResourcePatch())->jsonSerialize());
    }

    #[Test]
    public function testSerializesPreferencesWhenSet(): void
    {
        $payload = (new ResourcePatch(
            preferences: new UserPreferences(theme: ThemeMode::DARK),
        ))->jsonSerialize();

        self::assertArrayHasKey('preferences', $payload);
        self::assertSame('dark', $payload['preferences']['theme']);
        self::assertArrayNotHasKey('capabilities', $payload);
        self::assertArrayNotHasKey('featureFlags', $payload);
    }

    #[Test]
    public function testSerializesCapabilitiesAndFlagsWithMirroredKeys(): void
    {
        $payload = (new ResourcePatch(
            capabilities: ['user:edit' => true],
            featureFlags: ['beta' => false],
        ))->jsonSerialize();

        self::assertSame(['user:edit' => true], $payload['capabilities']);
        self::assertSame(['beta' => false], $payload['featureFlags']);
        self::assertArrayNotHasKey('preferences', $payload);
    }
}

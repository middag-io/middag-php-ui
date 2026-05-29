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

use Middag\Ui\Data\UserPreferences;
use Middag\Ui\Enum\ThemeMode;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @internal
 */
#[CoversClass(UserPreferences::class)]
final class UserPreferencesTest extends TestCase
{
    #[Test]
    public function testIsReadonlyClass(): void
    {
        self::assertTrue((new ReflectionClass(UserPreferences::class))->isReadOnly());
    }

    #[Test]
    public function testSerializesDefaults(): void
    {
        $payload = (new UserPreferences())->jsonSerialize();

        self::assertSame(['theme' => 'system', 'locale' => 'en'], $payload);
        self::assertArrayNotHasKey('timezone', $payload);
    }

    #[Test]
    public function testSerializesOptionalFieldsWhenSet(): void
    {
        $payload = (new UserPreferences(
            theme: ThemeMode::DARK,
            locale: 'pt-BR',
            timezone: 'America/Sao_Paulo',
            dateFormat: 'short',
            numberFormat: 'decimal',
        ))->jsonSerialize();

        self::assertSame('dark', $payload['theme']);
        self::assertSame('pt-BR', $payload['locale']);
        self::assertSame('America/Sao_Paulo', $payload['timezone']);
        self::assertSame('short', $payload['dateFormat']);
        self::assertSame('decimal', $payload['numberFormat']);
    }

    #[Test]
    public function testExtraIsMergedWithTypedPrecedence(): void
    {
        $payload = (new UserPreferences(
            locale: 'en',
            extra: ['density' => 'compact', 'locale' => 'IGNORED'],
        ))->jsonSerialize();

        self::assertSame('compact', $payload['density']);
        self::assertSame('en', $payload['locale']);
    }
}

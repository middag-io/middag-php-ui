<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Tests\Shared\Concerns;

use Middag\Ui\Shared\Concerns\ProvidesJsonSchema;
use Middag\Ui\Shared\Enum\ThemeMode;
use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversTrait(ProvidesJsonSchema::class)]
final class ProvidesJsonSchemaTest extends TestCase
{
    #[Test]
    public function testDerivesAStringEnumSchemaFromTheBackingCases(): void
    {
        $expected = [
            'type' => 'string',
            'enum' => array_map(static fn (ThemeMode $case): string => $case->value, ThemeMode::cases()),
        ];

        self::assertSame($expected, ThemeMode::jsonSchema());
    }
}

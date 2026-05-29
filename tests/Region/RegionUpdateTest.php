<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Tests\Region;

use Middag\Ui\Block\BlockDescriptor;
use Middag\Ui\Region\RegionUpdate;
use Middag\Ui\Shared\Enum\RegionUpdateMode;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @internal
 */
#[CoversClass(RegionUpdate::class)]
final class RegionUpdateTest extends TestCase
{
    #[Test]
    public function testIsReadonlyClass(): void
    {
        self::assertTrue((new ReflectionClass(RegionUpdate::class))->isReadOnly());
    }

    #[Test]
    public function testDefaultsToReplaceAndOmitsEmptyCollections(): void
    {
        $payload = (new RegionUpdate('content'))->jsonSerialize();

        self::assertSame(['region' => 'content', 'mode' => 'replace'], $payload);
    }

    #[Test]
    public function testReplaceCarriesBlocks(): void
    {
        $update = RegionUpdate::replace('content', $this->block('a'), $this->block('b'));

        self::assertSame(RegionUpdateMode::REPLACE, $update->mode);

        $payload = $update->jsonSerialize();

        self::assertSame('content', $payload['region']);
        self::assertCount(2, $payload['blocks']);
        self::assertSame('a', $payload['blocks'][0]['key']);
        self::assertArrayNotHasKey('keys', $payload);
    }

    #[Test]
    public function testAppendAndPrependSetMode(): void
    {
        self::assertSame('append', RegionUpdate::append('c', $this->block('x'))->jsonSerialize()['mode']);
        self::assertSame('prepend', RegionUpdate::prepend('c', $this->block('x'))->jsonSerialize()['mode']);
    }

    #[Test]
    public function testUpdateMatchesByKey(): void
    {
        $payload = RegionUpdate::update('content', $this->block('row-7'))->jsonSerialize();

        self::assertSame('update', $payload['mode']);
        self::assertSame('row-7', $payload['blocks'][0]['key']);
    }

    #[Test]
    public function testRemoveCarriesKeysNotBlocks(): void
    {
        $update = RegionUpdate::remove('content', 'row-1', 'row-2');

        self::assertSame(RegionUpdateMode::REMOVE, $update->mode);

        $payload = $update->jsonSerialize();

        self::assertSame(['row-1', 'row-2'], $payload['keys']);
        self::assertArrayNotHasKey('blocks', $payload);
    }

    private function block(string $key): BlockDescriptor
    {
        return new BlockDescriptor(type: 'metric_card', key: $key, data: []);
    }
}

<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.com.br>
 * @copyright   2026 MIDDAG (https://www.middag.com.br)
 * @license     proprietary
 */

namespace Middag\Ui\Tests\Data;

use Middag\Ui\Data\BlockDescriptor;
use Middag\Ui\Data\PageAction;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class BlockDescriptorTest extends TestCase
{
    #[Test]
    public function testSerializesRequiredFields(): void
    {
        $block = new BlockDescriptor(
            type: 'dense_table',
            key: 'test',
            data: ['columns' => []],
        );

        self::assertSame(
            ['type' => 'dense_table', 'key' => 'test', 'data' => ['columns' => []]],
            $block->jsonSerialize(),
        );
    }

    #[Test]
    public function testOmitsNullOptionalFields(): void
    {
        $block = new BlockDescriptor(
            type: 'dense_table',
            key: 'test',
            data: ['columns' => []],
        );

        $payload = $block->jsonSerialize();

        self::assertArrayNotHasKey('variant', $payload);
        self::assertArrayNotHasKey('title', $payload);
        self::assertArrayNotHasKey('subtitle', $payload);
        self::assertArrayNotHasKey('actions', $payload);
        self::assertArrayNotHasKey('meta', $payload);
    }

    #[Test]
    public function testIncludesVariantWhenSet(): void
    {
        $block = new BlockDescriptor(
            type: 'dense_table',
            key: 'test',
            data: [],
            variant: 'compact',
        );

        $payload = $block->jsonSerialize();

        self::assertArrayHasKey('variant', $payload);
        self::assertSame('compact', $payload['variant']);
    }

    #[Test]
    public function testIncludesTitleAndSubtitle(): void
    {
        $block = new BlockDescriptor(
            type: 'dense_table',
            key: 'test',
            data: [],
            title: 'T',
            subtitle: 'S',
        );

        $payload = $block->jsonSerialize();

        self::assertSame('T', $payload['title']);
        self::assertSame('S', $payload['subtitle']);
    }

    #[Test]
    public function testSerializesActions(): void
    {
        $action = new PageAction(id: 'a', label: 'A', intent: 'primary');

        $block = new BlockDescriptor(
            type: 'dense_table',
            key: 'test',
            data: [],
            actions: [$action],
        );

        $payload = $block->jsonSerialize();

        self::assertArrayHasKey('actions', $payload);
        self::assertCount(1, $payload['actions']);
        self::assertSame('a', $payload['actions'][0]['id']);
        self::assertSame('A', $payload['actions'][0]['label']);
        self::assertSame('primary', $payload['actions'][0]['intent']);
    }

    #[Test]
    public function testIncludesMetaWhenNonEmpty(): void
    {
        $block = new BlockDescriptor(
            type: 'dense_table',
            key: 'test',
            data: [],
            meta: ['lazyProp' => 'invoices'],
        );

        $payload = $block->jsonSerialize();

        self::assertArrayHasKey('meta', $payload);
        self::assertSame(['lazyProp' => 'invoices'], $payload['meta']);
    }
}

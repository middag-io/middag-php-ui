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

use Middag\Ui\Data\InspectorDescriptor;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(InspectorDescriptor::class)]
final class InspectorDescriptorTest extends TestCase
{
    #[Test]
    public function testSerializesWithDefaults(): void
    {
        $inspector = new InspectorDescriptor(endpoint: '/api/{id}');

        self::assertSame(
            ['endpoint' => '/api/{id}', 'width' => 440],
            $inspector->jsonSerialize(),
        );
    }

    #[Test]
    public function testSerializesCustomWidth(): void
    {
        $inspector = new InspectorDescriptor(endpoint: '/api/{id}', width: 600);

        $payload = $inspector->jsonSerialize();

        self::assertSame(600, $payload['width']);
    }
}

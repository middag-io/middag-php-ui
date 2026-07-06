<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Tests\Inspector;

use Middag\Ui\Inspector\InspectorDescriptor;
use Middag\Ui\Region\PollConfig;
use Middag\Ui\Tests\Support\ValidatesAgainstSchema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(InspectorDescriptor::class)]
final class InspectorDescriptorTest extends TestCase
{
    use ValidatesAgainstSchema;

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

    #[Test]
    public function testSerializesPollWhenSet(): void
    {
        $inspector = new InspectorDescriptor(
            endpoint: '/api/{id}',
            poll: new PollConfig(endpoint: '/poll', intervalMs: 3000),
        );

        $payload = $inspector->jsonSerialize();

        self::assertArrayHasKey('poll', $payload);
        self::assertSame('/poll', $payload['poll']['endpoint']);
    }

    #[Test]
    public function testSchemaAcceptsADefaultInspector(): void
    {
        $this->assertValidAgainst('InspectorDescriptor', new InspectorDescriptor(endpoint: '/api/{id}'));
    }

    #[Test]
    public function testSchemaAcceptsAnInspectorWithPoll(): void
    {
        $this->assertValidAgainst('InspectorDescriptor', new InspectorDescriptor(
            endpoint: '/api/{id}',
            poll: new PollConfig(endpoint: '/poll', intervalMs: 3000),
        ));
    }

    #[Test]
    public function testSchemaRejectsAnInspectorMissingItsEndpoint(): void
    {
        $this->assertInvalidAgainst('InspectorDescriptor', ['width' => 440]);
    }

    #[Test]
    public function testSchemaRejectsANonIntegerWidth(): void
    {
        $this->assertInvalidAgainst('InspectorDescriptor', ['endpoint' => '/api/{id}', 'width' => '440']);
    }

    #[Test]
    public function testSchemaRejectsAnUnknownProperty(): void
    {
        $this->assertInvalidAgainst('InspectorDescriptor', ['endpoint' => '/api/{id}', 'width' => 440, 'height' => 200]);
    }
}

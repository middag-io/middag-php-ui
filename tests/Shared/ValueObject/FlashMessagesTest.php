<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Tests\Shared\ValueObject;

use Middag\Ui\Shared\ValueObject\FlashMessages;
use Middag\Ui\Tests\Support\ValidatesAgainstSchema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @internal
 */
#[CoversClass(FlashMessages::class)]
final class FlashMessagesTest extends TestCase
{
    use ValidatesAgainstSchema;

    #[Test]
    public function testIsReadonlyClass(): void
    {
        self::assertTrue((new ReflectionClass(FlashMessages::class))->isReadOnly());
    }

    #[Test]
    public function testEmptyEmitsAnObjectNotAnArray(): void
    {
        self::assertSame('{}', json_encode(new FlashMessages()));
        $this->assertValidAgainst('FlashMessages', new FlashMessages());
    }

    #[Test]
    public function testEmitsOnlySetKeys(): void
    {
        $flash = new FlashMessages(success: 'Saved', warning: 'Careful');

        self::assertSame('{"success":"Saved","warning":"Careful"}', json_encode($flash));
        $this->assertValidAgainst('FlashMessages', $flash);
    }

    #[Test]
    public function testFromArrayDiscardsUnknownKeysAndNonStringValues(): void
    {
        $flash = FlashMessages::fromArray([
            'success' => 'Saved',
            'error' => ['not', 'a', 'string'],
            'info' => 42,
            'toast' => 'dropped',
            'webhooks_secret_reveal' => 'dropped',
            'warning' => 'Careful',
        ]);

        self::assertSame('{"success":"Saved","warning":"Careful"}', json_encode($flash));
    }

    #[Test]
    public function testFromArrayOfNothingUsableIsAnEmptyObject(): void
    {
        self::assertSame('{}', json_encode(FlashMessages::fromArray(['x' => 'y', 'error' => null])));
    }

    #[Test]
    public function testSchemaRejectsToastAndNonStringValues(): void
    {
        $this->assertInvalidAgainst('FlashMessages', ['toast' => ['severity' => 'info']]);
        $this->assertInvalidAgainst('FlashMessages', ['success' => 1]);
    }
}

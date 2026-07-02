<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Tests\Action;

use Middag\Ui\Action\Confirmation;
use Middag\Ui\Shared\ValueObject\Translatable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @internal
 */
#[CoversClass(Confirmation::class)]
final class ConfirmationTest extends TestCase
{
    #[Test]
    public function testIsReadonlyClass(): void
    {
        self::assertTrue((new ReflectionClass(Confirmation::class))->isReadOnly());
    }

    #[Test]
    public function testSerializesMinimal(): void
    {
        $payload = (new Confirmation(title: 'Delete?', message: 'Sure?'))->jsonSerialize();

        self::assertSame([
            'title' => 'Delete?',
            'message' => 'Sure?',
            'variant' => 'default',
        ], $payload);
    }

    #[Test]
    public function testSerializesAllFields(): void
    {
        $payload = (new Confirmation(
            title: Translatable::of('del_title', 'local_x'),
            message: 'Sure?',
            confirmLabel: 'Yes',
            cancelLabel: Translatable::of('cancel', 'local_x'),
            variant: 'danger',
        ))->jsonSerialize();

        self::assertSame(['key' => 'del_title', 'domain' => 'local_x'], $payload['title']);
        self::assertSame('Yes', $payload['confirmLabel']);
        self::assertSame(['key' => 'cancel', 'domain' => 'local_x'], $payload['cancelLabel']);
        self::assertSame('danger', $payload['variant']);
    }
}

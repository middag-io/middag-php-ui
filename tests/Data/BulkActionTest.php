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

use Middag\Ui\Data\BulkAction;
use Middag\Ui\Data\Confirmation;
use Middag\Ui\Data\Translatable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @internal
 */
#[CoversClass(BulkAction::class)]
final class BulkActionTest extends TestCase
{
    #[Test]
    public function testIsReadonlyClass(): void
    {
        self::assertTrue((new ReflectionClass(BulkAction::class))->isReadOnly());
    }

    #[Test]
    public function testSerializesMinimal(): void
    {
        $payload = (new BulkAction(id: 'delete', label: 'Delete', intent: 'danger', endpoint: '/x/bulk-delete'))->jsonSerialize();

        self::assertSame([
            'id' => 'delete',
            'label' => 'Delete',
            'intent' => 'danger',
            'endpoint' => '/x/bulk-delete',
            'method' => 'post',
        ], $payload);
    }

    #[Test]
    public function testSerializesConfirmationAndCapability(): void
    {
        $payload = (new BulkAction(
            id: 'delete',
            label: Translatable::of('bulk_delete', 'local_x'),
            intent: 'danger',
            endpoint: '/x/bulk-delete',
            method: 'delete',
            confirmation: new Confirmation(title: 'Delete?', message: 'Sure?', variant: 'danger'),
            capability: 'manage_items',
        ))->jsonSerialize();

        self::assertSame(['key' => 'bulk_delete', 'domain' => 'local_x'], $payload['label']);
        self::assertSame('delete', $payload['method']);
        self::assertSame('Delete?', $payload['confirmation']['title']);
        self::assertSame('manage_items', $payload['capability']);
    }
}

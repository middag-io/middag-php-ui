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

use Middag\Ui\Action\Action;
use Middag\Ui\Action\ActionTarget;
use Middag\Ui\Action\Confirmation;
use Middag\Ui\Shared\Enum\ActionIntent;
use Middag\Ui\Shared\Enum\HttpMethod;
use Middag\Ui\Shared\ValueObject\Translatable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @internal
 */
#[CoversClass(Action::class)]
final class ActionTest extends TestCase
{
    #[Test]
    public function testIsReadonlyClass(): void
    {
        self::assertTrue((new ReflectionClass(Action::class))->isReadOnly());
    }

    #[Test]
    public function testSerializesMinimal(): void
    {
        $action = new Action(id: 'create', label: 'Create', target: ActionTarget::link('/x/create'));

        self::assertSame([
            'id' => 'create',
            'label' => 'Create',
            'target' => ['kind' => 'link', 'href' => '/x/create'],
            'intent' => 'secondary',
        ], $action->jsonSerialize());
    }

    #[Test]
    public function testSerializesAllFields(): void
    {
        $action = new Action(
            id: 'delete',
            label: Translatable::of('btn_delete', 'local_x'),
            target: ActionTarget::request('/x/{id}', HttpMethod::Delete),
            intent: ActionIntent::Danger,
            icon: 'trash',
            confirmation: new Confirmation(title: 'Delete?', message: 'Sure?', variant: 'danger'),
            capability: 'manage_x',
            disabled: true,
            loading: true,
        );

        $payload = $action->jsonSerialize();

        self::assertSame(['key' => 'btn_delete', 'domain' => 'local_x'], $payload['label']);
        self::assertSame('request', $payload['target']['kind']);
        self::assertSame('delete', $payload['target']['method']);
        self::assertSame('danger', $payload['intent']);
        self::assertSame('trash', $payload['icon']);
        self::assertSame('Delete?', $payload['confirmation']['title']);
        self::assertSame('manage_x', $payload['capability']);
        self::assertTrue($payload['disabled']);
        self::assertTrue($payload['loading']);
    }

    #[Test]
    public function testOmitsOptionalsWhenDefault(): void
    {
        $payload = (new Action(id: 'x', label: 'X', target: ActionTarget::route('x.index')))->jsonSerialize();

        self::assertArrayNotHasKey('icon', $payload);
        self::assertArrayNotHasKey('confirmation', $payload);
        self::assertArrayNotHasKey('capability', $payload);
        self::assertArrayNotHasKey('disabled', $payload);
        self::assertArrayNotHasKey('loading', $payload);
    }
}

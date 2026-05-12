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

use Middag\Ui\Data\PageAction;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(PageAction::class)]
final class PageActionTest extends TestCase
{
    #[Test]
    public function testSerializesRequiredFields(): void
    {
        $action = new PageAction(id: 'create', label: 'Create', intent: 'primary');

        $payload = $action->jsonSerialize();

        self::assertSame('create', $payload['id']);
        self::assertSame('Create', $payload['label']);
        self::assertSame('primary', $payload['intent']);
    }

    #[Test]
    public function testIncludesHrefWhenSet(): void
    {
        $action = new PageAction(
            id: 'create',
            label: 'Create',
            intent: 'primary',
            href: '/create',
        );

        $payload = $action->jsonSerialize();

        self::assertArrayHasKey('href', $payload);
        self::assertSame('/create', $payload['href']);
    }

    #[Test]
    public function testIncludesMethodWhenSet(): void
    {
        $action = new PageAction(
            id: 'delete',
            label: 'Delete',
            intent: 'danger',
            method: 'POST',
        );

        $payload = $action->jsonSerialize();

        self::assertArrayHasKey('method', $payload);
        self::assertSame('POST', $payload['method']);
    }

    #[Test]
    public function testIncludesIconWhenSet(): void
    {
        $action = new PageAction(
            id: 'create',
            label: 'Create',
            intent: 'primary',
            icon: 'plus',
        );

        $payload = $action->jsonSerialize();

        self::assertArrayHasKey('icon', $payload);
        self::assertSame('plus', $payload['icon']);
    }

    #[Test]
    public function testIncludesRequiresConfirmationWhenTrue(): void
    {
        $action = new PageAction(
            id: 'delete',
            label: 'Delete',
            intent: 'danger',
            requires_confirmation: true,
        );

        $payload = $action->jsonSerialize();

        self::assertArrayHasKey('requires_confirmation', $payload);
        self::assertTrue($payload['requires_confirmation']);
    }

    #[Test]
    public function testOmitsBooleansWhenFalse(): void
    {
        $action = new PageAction(
            id: 'create',
            label: 'Create',
            intent: 'primary',
            disabled: false,
            loading: false,
        );

        $payload = $action->jsonSerialize();

        self::assertArrayNotHasKey('disabled', $payload);
        self::assertArrayNotHasKey('loading', $payload);
    }
}

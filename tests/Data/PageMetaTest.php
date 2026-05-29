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

use Middag\Ui\Data\Action;
use Middag\Ui\Data\ActionTarget;
use Middag\Ui\Data\Breadcrumb;
use Middag\Ui\Data\PageMeta;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(PageMeta::class)]
final class PageMetaTest extends TestCase
{
    #[Test]
    public function testSerializesRequiredFields(): void
    {
        $meta = new PageMeta(key: 'users.index', title: 'Users');

        $payload = $meta->jsonSerialize();

        self::assertSame('users.index', $payload['key']);
        self::assertSame('Users', $payload['title']);
    }

    #[Test]
    public function testOmitsSubtitleWhenNull(): void
    {
        $meta = new PageMeta(key: 'users.index', title: 'Users');

        $payload = $meta->jsonSerialize();

        self::assertArrayNotHasKey('subtitle', $payload);
    }

    #[Test]
    public function testIncludesSubtitle(): void
    {
        $meta = new PageMeta(
            key: 'users.show',
            title: 'User',
            subtitle: 'Details',
        );

        $payload = $meta->jsonSerialize();

        self::assertArrayHasKey('subtitle', $payload);
        self::assertSame('Details', $payload['subtitle']);
    }

    #[Test]
    public function testSerializesBreadcrumbs(): void
    {
        $meta = new PageMeta(
            key: 'users.show',
            title: 'User',
            breadcrumbs: [
                new Breadcrumb(label: 'Home', href: '/'),
                new Breadcrumb(label: 'Users', href: '/users'),
            ],
        );

        $payload = $meta->jsonSerialize();

        self::assertArrayHasKey('breadcrumbs', $payload);
        self::assertCount(2, $payload['breadcrumbs']);
        self::assertSame('Home', $payload['breadcrumbs'][0]['label']);
        self::assertSame('/users', $payload['breadcrumbs'][1]['href']);
    }

    #[Test]
    public function testSerializesActions(): void
    {
        $meta = new PageMeta(
            key: 'users.index',
            title: 'Users',
            actions: [
                new Action(id: 'create', label: 'Create', target: ActionTarget::link('/users/create')),
            ],
        );

        $payload = $meta->jsonSerialize();

        self::assertArrayHasKey('actions', $payload);
        self::assertCount(1, $payload['actions']);
        self::assertSame('create', $payload['actions'][0]['id']);
    }

    #[Test]
    public function testOmitsEmptyArrays(): void
    {
        $meta = new PageMeta(
            key: 'users.index',
            title: 'Users',
            breadcrumbs: [],
            actions: [],
        );

        $payload = $meta->jsonSerialize();

        self::assertArrayNotHasKey('breadcrumbs', $payload);
        self::assertArrayNotHasKey('actions', $payload);
    }
}

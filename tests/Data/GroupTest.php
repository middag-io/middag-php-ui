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

use Middag\Ui\Contract\FieldInterface;
use Middag\Ui\Contract\LayoutElementInterface;
use Middag\Ui\Data\Group;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(Group::class)]
final class GroupTest extends TestCase
{
    #[Test]
    public function testIdReturnedFromFactory(): void
    {
        $group = Group::of('address');

        self::assertSame('address', $group->id());
    }

    #[Test]
    public function testChildrenEmptyByDefault(): void
    {
        $group = Group::of('g');

        self::assertSame([], $group->children());
    }

    #[Test]
    public function testFieldsReplacesChildren(): void
    {
        $field = $this->createStub(FieldInterface::class);

        $group = Group::of('g');
        $group->fields($field);

        self::assertCount(1, $group->children());
        self::assertSame($field, $group->children()[0]);
    }

    #[Test]
    public function testFieldsAcceptsMultiple(): void
    {
        $f1 = $this->createStub(FieldInterface::class);
        $f2 = $this->createStub(FieldInterface::class);

        $group = Group::of('g');
        $group->fields($f1, $f2);

        self::assertCount(2, $group->children());
    }

    #[Test]
    public function testFieldsAcceptsLayoutElements(): void
    {
        $nested = $this->createStub(LayoutElementInterface::class);

        $group = Group::of('g');
        $group->fields($nested);

        self::assertSame($nested, $group->children()[0]);
    }

    #[Test]
    public function testImplementsLayoutElementInterface(): void
    {
        self::assertInstanceOf(LayoutElementInterface::class, Group::of('g'));
    }
}

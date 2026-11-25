<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Tests\Page;

use Middag\Ui\Page\PageBuilder;
use Middag\Ui\Table\CrudBuilder;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

/**
 * The static factories instantiate the called class, so an empty
 * pass-through subclass gets an instance of itself.
 *
 * @internal
 */
#[CoversClass(PageBuilder::class)]
#[CoversClass(CrudBuilder::class)]
final class BuilderSubclassTest extends TestCase
{
    #[Test]
    public function pageFactoryReturnsTheCalledClass(): void
    {
        $builder = SubclassedPageBuilder::page('p');

        self::assertInstanceOf(SubclassedPageBuilder::class, $builder);
        self::assertSame('p', $builder->build()->page->key);
    }

    #[Test]
    public function crudFactoryReturnsTheCalledClass(): void
    {
        self::assertInstanceOf(SubclassedCrudBuilder::class, SubclassedCrudBuilder::for('App\Entity\Ticket'));
    }

    #[Test]
    public function factoriesDeclareAStaticReturnType(): void
    {
        self::assertSame('static', (string) (new ReflectionMethod(PageBuilder::class, 'page'))->getReturnType());
        self::assertSame('static', (string) (new ReflectionMethod(CrudBuilder::class, 'for'))->getReturnType());
    }

    #[Test]
    public function baseFactoriesStillReturnTheBaseClass(): void
    {
        self::assertSame(PageBuilder::class, PageBuilder::page('p')::class);
        self::assertSame(CrudBuilder::class, CrudBuilder::for('App\Entity\Ticket')::class);
    }
}

/**
 * @internal
 */
final class SubclassedPageBuilder extends PageBuilder {}

/**
 * @internal
 */
final class SubclassedCrudBuilder extends CrudBuilder {}

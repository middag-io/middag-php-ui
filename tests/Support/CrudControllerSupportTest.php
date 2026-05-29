<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Tests\Support;

use Middag\Ui\Builder\CrudBuilder;
use Middag\Ui\PageContract;
use Middag\Ui\Support\CrudControllerSupport;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(CrudControllerSupport::class)]
final class CrudControllerSupportTest extends TestCase
{
    #[Test]
    public function testIndexBuildsIndexPage(): void
    {
        $crud = CrudBuilder::for('App\Entity\Invoice');
        $contract = CrudControllerSupport::index($crud, [['name' => 'INV-001']], ['page' => 1]);

        self::assertInstanceOf(PageContract::class, $contract);
        self::assertSame('invoices.index', $contract->page->key);
    }

    #[Test]
    public function testCreateBuildsCreatePage(): void
    {
        $crud = CrudBuilder::for('App\Entity\Invoice');
        $contract = CrudControllerSupport::create($crud, ['fields' => []]);

        self::assertInstanceOf(PageContract::class, $contract);
        self::assertSame('invoices.create', $contract->page->key);
    }

    #[Test]
    public function testEditBuildsEditPage(): void
    {
        $crud = CrudBuilder::for('App\Entity\Invoice');
        $contract = CrudControllerSupport::edit($crud, 42, ['name' => 'INV-001'], ['fields' => []], []);

        self::assertInstanceOf(PageContract::class, $contract);
        self::assertSame('invoices.edit', $contract->page->key);
    }

    #[Test]
    public function testShowBuildsShowPage(): void
    {
        $crud = CrudBuilder::for('App\Entity\Invoice');
        $contract = CrudControllerSupport::show($crud, ['id' => 42], []);

        self::assertInstanceOf(PageContract::class, $contract);
        self::assertSame('invoices.show', $contract->page->key);
    }
}

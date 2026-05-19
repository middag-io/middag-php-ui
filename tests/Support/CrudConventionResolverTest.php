<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.com.br>
 * @copyright   2026 MIDDAG (https://www.middag.com.br)
 * @license     proprietary
 */

namespace Middag\Ui\Tests\Support;

use Middag\Ui\Support\CrudConventionResolver;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(CrudConventionResolver::class)]
final class CrudConventionResolverTest extends TestCase
{
    #[Test]
    public function testSlugPluralizesAndLowercases(): void
    {
        self::assertSame('invoices', CrudConventionResolver::slug('App\Entity\Invoice'));
        self::assertSame('orders', CrudConventionResolver::slug('Order'));
    }

    #[Test]
    public function testTitleUcfirstsSlug(): void
    {
        self::assertSame('Invoices', CrudConventionResolver::title('App\Entity\Invoice'));
    }

    #[Test]
    public function testSingularReturnsUcfirstBasename(): void
    {
        self::assertSame('Invoice', CrudConventionResolver::singular('App\Entity\Invoice'));
    }

    #[Test]
    public function testColumnsReturnsEmptyForMissingClass(): void
    {
        self::assertSame([], CrudConventionResolver::columns('Nonexistent\\Entity'));
    }

    #[Test]
    public function testColumnsExcludesConventionalHiddenFields(): void
    {
        $columns = CrudConventionResolver::columns(CrudConventionResolverTestEntity::class);

        self::assertContains('name', $columns);
        self::assertContains('amount', $columns);
        self::assertNotContains('id', $columns);
        self::assertNotContains('timecreated', $columns);
        self::assertNotContains('timemodified', $columns);
        self::assertNotContains('usermodified', $columns);
    }

    #[Test]
    public function testFormClassReturnsNullWhenNoExtensionsSegment(): void
    {
        self::assertNull(CrudConventionResolver::form_class('App\Entity\Invoice'));
    }

    #[Test]
    public function testCapabilityFollowsLocalMiddagConvention(): void
    {
        self::assertSame('local/middag:manage_invoice', CrudConventionResolver::capability('App\Entity\Invoice'));
    }

    #[Test]
    public function testRoutePrefixEqualsSlug(): void
    {
        self::assertSame('invoices', CrudConventionResolver::route_prefix('App\Entity\Invoice'));
    }
}

/**
 * Synthetic entity for column discovery test.
 */
final class CrudConventionResolverTestEntity
{
    public int $id = 0;

    public string $name = '';

    public float $amount = 0.0;

    public int $timecreated = 0;

    public int $timemodified = 0;

    public int $usermodified = 0;
}

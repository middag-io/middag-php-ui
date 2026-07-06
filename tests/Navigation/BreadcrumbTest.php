<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Tests\Navigation;

use Middag\Ui\Navigation\Breadcrumb;
use Middag\Ui\Tests\Support\ValidatesAgainstSchema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(Breadcrumb::class)]
final class BreadcrumbTest extends TestCase
{
    use ValidatesAgainstSchema;

    #[Test]
    public function testSerializesLabelOnly(): void
    {
        $breadcrumb = new Breadcrumb(label: 'Home');

        self::assertSame(['label' => 'Home'], $breadcrumb->jsonSerialize());
    }

    #[Test]
    public function testSerializesWithHref(): void
    {
        $breadcrumb = new Breadcrumb(label: 'Home', href: '/');

        self::assertSame(['label' => 'Home', 'href' => '/'], $breadcrumb->jsonSerialize());
    }

    #[Test]
    public function testSerializesExternalLink(): void
    {
        $breadcrumb = new Breadcrumb(
            label: 'Docs',
            href: 'https://docs.middag.io',
            external: true,
        );

        $payload = $breadcrumb->jsonSerialize();

        self::assertSame('Docs', $payload['label']);
        self::assertSame('https://docs.middag.io', $payload['href']);
        self::assertTrue($payload['external']);
    }

    #[Test]
    public function testOmitsExternalWhenFalse(): void
    {
        $breadcrumb = new Breadcrumb(label: 'Home', href: '/', external: false);

        $payload = $breadcrumb->jsonSerialize();

        self::assertArrayNotHasKey('external', $payload);
    }

    #[Test]
    public function testSchemaAcceptsALabelOnlyCrumb(): void
    {
        $this->assertValidAgainst('Breadcrumb', new Breadcrumb(label: 'Home'));
    }

    #[Test]
    public function testSchemaAcceptsAnExternalLinkedCrumb(): void
    {
        $this->assertValidAgainst('Breadcrumb', new Breadcrumb(
            label: 'Docs',
            href: 'https://docs.middag.io',
            external: true,
        ));
    }

    #[Test]
    public function testSchemaRejectsACrumbMissingItsLabel(): void
    {
        $this->assertInvalidAgainst('Breadcrumb', ['href' => '/']);
    }

    #[Test]
    public function testSchemaRejectsExternalFalseSinceOnlyTrueIsEmitted(): void
    {
        $this->assertInvalidAgainst('Breadcrumb', ['label' => 'Home', 'href' => '/', 'external' => false]);
    }

    #[Test]
    public function testSchemaRejectsAnUnknownProperty(): void
    {
        $this->assertInvalidAgainst('Breadcrumb', ['label' => 'Home', 'target' => '_blank']);
    }
}

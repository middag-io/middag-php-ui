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

use Middag\Ui\Navigation\Contract\NavigationRegistryInterface;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use ReflectionParameter;

/**
 * Pins the registration signatures of NavigationRegistryInterface: it is an
 * implementable contract, and named arguments bind to the parameter names.
 *
 * @internal
 */
#[CoversNothing]
final class NavigationRegistryInterfaceTest extends TestCase
{
    private const array LEVEL = [
        'key: string',
        'label: Middag\Ui\Shared\ValueObject\Translatable|string',
        'icon: ?string = null',
        'weight: int = 50',
        'entityType: ?string = null',
        'badgeVariant: ?string = null',
        'statusColor: ?string = null',
    ];

    /**
     * @param list<string> $expected
     */
    #[Test]
    #[DataProvider('signatures')]
    public function testRegistrationSignatureIsPinned(string $method, array $expected): void
    {
        $actual = array_map(
            static fn (ReflectionParameter $p): string => $p->getName() . ': ' . $p->getType()
                . ($p->isDefaultValueAvailable() ? ' = ' . json_encode($p->getDefaultValue()) : ''),
            (new ReflectionMethod(NavigationRegistryInterface::class, $method))->getParameters(),
        );

        self::assertSame($expected, $actual);
    }

    /**
     * @return iterable<string, array{string, list<string>}>
     */
    public static function signatures(): iterable
    {
        yield 'group' => ['group', self::LEVEL];

        yield 'section' => ['section', self::LEVEL];

        yield 'item' => ['item', [
            'key: string',
            'label: Middag\Ui\Shared\ValueObject\Translatable|string',
            'route: ?string',
            'routeParams: array = []',
            'icon: ?string = null',
            'weight: int = 50',
            'entityType: ?string = null',
            'badgeVariant: ?string = null',
            'statusColor: ?string = null',
            'target: ?string = null',
            'href: ?string = null',
        ]];
    }
}

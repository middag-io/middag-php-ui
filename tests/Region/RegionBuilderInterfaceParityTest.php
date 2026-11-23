<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Tests\Region;

use Middag\Ui\Block\BlockBuilder;
use Middag\Ui\Region\Contract\RegionBuilderInterface;
use Middag\Ui\Region\RegionBuilder;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;

/**
 * The builders are a call surface: callers use positional and named
 * arguments against RegionBuilderInterface, RegionBuilder and the
 * BlockBuilder statics alike, so the three must agree on parameter names,
 * order and defaults.
 *
 * @internal
 */
#[CoversClass(RegionBuilder::class)]
#[CoversClass(BlockBuilder::class)]
final class RegionBuilderInterfaceParityTest extends TestCase
{
    /**
     * Public RegionBuilder methods that are deliberately not on the interface:
     * adding a method to the interface breaks its implementors.
     */
    private const array NOT_ON_INTERFACE = ['poll'];

    #[Test]
    public function testEveryInterfaceMethodMatchesRegionBuilder(): void
    {
        $builder = new ReflectionClass(RegionBuilder::class);

        foreach ((new ReflectionClass(RegionBuilderInterface::class))->getMethods() as $method) {
            $name = $method->getName();
            self::assertTrue($builder->hasMethod($name), sprintf('RegionBuilder lacks interface method %s().', $name));
            self::assertSame(
                $this->signature($method),
                $this->signature($builder->getMethod($name)),
                sprintf('RegionBuilder::%s() must match RegionBuilderInterface::%s().', $name, $name),
            );
        }
    }

    #[Test]
    public function testEveryPublicRegionBuilderMethodIsOnTheInterface(): void
    {
        $interface = new ReflectionClass(RegionBuilderInterface::class);

        foreach ($this->ownPublicMethods(RegionBuilder::class) as $method) {
            $name = $method->getName();
            if (in_array($name, self::NOT_ON_INTERFACE, true)) {
                continue;
            }

            self::assertTrue($interface->hasMethod($name), sprintf('RegionBuilder::%s() is not on RegionBuilderInterface.', $name));
        }
    }

    #[Test]
    public function testRegionBuilderMatchesTheBlockBuilderStaticsItDelegatesTo(): void
    {
        $blocks = new ReflectionClass(BlockBuilder::class);
        $compared = 0;

        foreach ($this->ownPublicMethods(RegionBuilder::class) as $method) {
            $name = $method->getName();
            if (!$blocks->hasMethod($name)) {
                continue;
            }

            $static = $blocks->getMethod($name);
            self::assertTrue($static->isStatic());
            self::assertSame(
                $this->signature($static),
                $this->signature($method),
                sprintf('RegionBuilder::%s() must match BlockBuilder::%s().', $name, $name),
            );
            ++$compared;
        }

        self::assertGreaterThan(0, $compared);
    }

    /**
     * @param class-string $class
     *
     * @return list<ReflectionMethod>
     */
    private function ownPublicMethods(string $class): array
    {
        return array_values(array_filter(
            (new ReflectionClass($class))->getMethods(ReflectionMethod::IS_PUBLIC),
            static fn (ReflectionMethod $m): bool => $m->getDeclaringClass()->getName() === $class && !$m->isConstructor(),
        ));
    }

    /** @return list<array{name: string, optional: bool, default: mixed}> */
    private function signature(ReflectionMethod $method): array
    {
        return array_map(
            static fn (ReflectionParameter $p): array => [
                'name' => $p->getName(),
                'optional' => $p->isOptional(),
                'default' => $p->isDefaultValueAvailable() ? $p->getDefaultValue() : null,
            ],
            $method->getParameters(),
        );
    }
}

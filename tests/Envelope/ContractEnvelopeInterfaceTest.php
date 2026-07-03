<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Tests\Envelope;

use JsonSerializable;
use Middag\Ui\Envelope\Contract\ContractEnvelopeInterface;
use Middag\Ui\Page\Contract\PageContractInterface;
use Middag\Ui\Region\Fragment;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;

/**
 * @internal
 */
#[CoversNothing]
final class ContractEnvelopeInterfaceTest extends TestCase
{
    #[Test]
    public function isAJsonSerializableInterface(): void
    {
        $reflection = new ReflectionClass(ContractEnvelopeInterface::class);

        self::assertTrue($reflection->isInterface());
        self::assertTrue($reflection->implementsInterface(JsonSerializable::class));
    }

    #[Test]
    public function wireSurfaceIsExactlyJsonSerialize(): void
    {
        $reflection = new ReflectionClass(ContractEnvelopeInterface::class);

        // The whole callable surface is jsonSerialize(), inherited from
        // JsonSerializable — the envelope contract adds no methods of its own.
        $methods = array_map(
            static fn (ReflectionMethod $method): string => $method->getName(),
            $reflection->getMethods(),
        );
        self::assertSame(['jsonSerialize'], $methods);

        $jsonSerialize = $reflection->getMethod('jsonSerialize');
        self::assertSame(JsonSerializable::class, $jsonSerialize->getDeclaringClass()->getName());
        self::assertSame(0, $jsonSerialize->getNumberOfParameters());
    }

    #[Test]
    public function declaresSinglePublicVersionConstant(): void
    {
        $reflection = new ReflectionClass(ContractEnvelopeInterface::class);

        self::assertSame(['VERSION' => '1'], $reflection->getConstants());
        self::assertTrue($reflection->getReflectionConstant('VERSION')->isPublic());
    }

    #[Test]
    public function fullAndPartialEnvelopesShareTheSameVersion(): void
    {
        // The docblock promise: the full envelope (PageContract) and the
        // partial envelope (Fragment) both derive from this interface, so
        // their wire versions can never drift apart.
        self::assertTrue(is_subclass_of(PageContractInterface::class, ContractEnvelopeInterface::class));
        self::assertTrue(is_subclass_of(Fragment::class, ContractEnvelopeInterface::class));

        self::assertSame(ContractEnvelopeInterface::VERSION, PageContractInterface::VERSION);
        self::assertSame(ContractEnvelopeInterface::VERSION, Fragment::VERSION);
    }

    #[Test]
    public function anonymousImplementationCarriesVersionOnTheWire(): void
    {
        $envelope = new class implements ContractEnvelopeInterface {
            /** @return array<string, mixed> */
            public function jsonSerialize(): array
            {
                return [
                    'version' => self::VERSION,
                    'kind' => 'probe',
                ];
            }
        };

        self::assertInstanceOf(JsonSerializable::class, $envelope);

        $decoded = json_decode(json_encode($envelope, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR);

        self::assertIsArray($decoded);
        self::assertSame(ContractEnvelopeInterface::VERSION, $decoded['version']);
        self::assertSame('1', $decoded['version']);
        self::assertSame('probe', $decoded['kind']);
    }
}

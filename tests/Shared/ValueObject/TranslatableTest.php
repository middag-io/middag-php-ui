<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Tests\Shared\ValueObject;

use Middag\Ui\Shared\ValueObject\Translatable;
use Middag\Ui\Tests\Support\ValidatesAgainstSchema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @internal
 */
#[CoversClass(Translatable::class)]
final class TranslatableTest extends TestCase
{
    use ValidatesAgainstSchema;

    #[Test]
    public function testIsReadonlyClass(): void
    {
        self::assertTrue((new ReflectionClass(Translatable::class))->isReadOnly());
    }

    #[Test]
    public function testConstructAndNamedConstructorAreEquivalent(): void
    {
        $a = new Translatable('save', 'local_x', ['name' => 'X']);
        $b = Translatable::of('save', 'local_x', ['name' => 'X']);

        self::assertEquals($a, $b);
        self::assertSame('save', $b->key);
        self::assertSame('local_x', $b->domain);
        self::assertSame(['name' => 'X'], $b->params);
    }

    #[Test]
    public function testSerializeOmitsEmptyParams(): void
    {
        $payload = Translatable::of('save', 'local_x')->jsonSerialize();

        self::assertSame(['key' => 'save', 'domain' => 'local_x'], $payload);
        self::assertArrayNotHasKey('params', $payload);
    }

    #[Test]
    public function testSerializeIncludesParamsWhenSet(): void
    {
        $payload = Translatable::of('greeting', 'local_x', ['name' => 'Ada'])->jsonSerialize();

        self::assertSame([
            'key' => 'greeting',
            'domain' => 'local_x',
            'params' => ['name' => 'Ada'],
        ], $payload);
    }

    #[Test]
    public function testSchemaAcceptsAMinimalTranslatable(): void
    {
        $this->assertValidAgainst('Translatable', Translatable::of('save', 'local_x'));
    }

    #[Test]
    public function testSchemaAcceptsParamsAsAFreeFormMap(): void
    {
        $this->assertValidAgainst('Translatable', Translatable::of('greeting', 'local_x', ['name' => 'Ada']));
    }

    #[Test]
    public function testSchemaRejectsAPayloadMissingTheDomain(): void
    {
        $this->assertInvalidAgainst('Translatable', ['key' => 'save']);
    }

    #[Test]
    public function testSchemaRejectsAnAdditionalProperty(): void
    {
        $this->assertInvalidAgainst('Translatable', ['key' => 'save', 'domain' => 'local_x', 'extra' => 1]);
    }
}

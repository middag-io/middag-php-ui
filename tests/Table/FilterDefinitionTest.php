<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Tests\Table;

use Middag\Ui\Shared\Enum\FilterType;
use Middag\Ui\Shared\ValueObject\Translatable;
use Middag\Ui\Table\FilterDefinition;
use Middag\Ui\Tests\Support\ValidatesAgainstSchema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @internal
 */
#[CoversClass(FilterDefinition::class)]
final class FilterDefinitionTest extends TestCase
{
    use ValidatesAgainstSchema;

    #[Test]
    public function testIsReadonlyClass(): void
    {
        self::assertTrue((new ReflectionClass(FilterDefinition::class))->isReadOnly());
    }

    #[Test]
    public function testSerializesMinimal(): void
    {
        $payload = (new FilterDefinition(key: 'status', label: 'Status'))->jsonSerialize();

        self::assertSame([
            'key' => 'status',
            'label' => 'Status',
            'type' => 'select',
        ], $payload);
        self::assertArrayNotHasKey('options', $payload);
    }

    #[Test]
    public function testSerializesAllFields(): void
    {
        $payload = (new FilterDefinition(
            key: 'status',
            label: Translatable::of('status', 'local_x'),
            type: FilterType::SELECT,
            options: [
                ['value' => 'active', 'label' => 'Active'],
                ['value' => 'archived', 'label' => Translatable::of('archived', 'local_x')],
            ],
            placeholder: 'Pick one',
            default: 'active',
        ))->jsonSerialize();

        self::assertSame(['key' => 'status', 'domain' => 'local_x'], $payload['label']);
        self::assertSame('active', $payload['options'][0]['value']);
        self::assertSame('Active', $payload['options'][0]['label']);
        self::assertSame(['key' => 'archived', 'domain' => 'local_x'], $payload['options'][1]['label']);
        self::assertSame('Pick one', $payload['placeholder']);
        self::assertSame('active', $payload['default']);
    }

    #[Test]
    public function testSchemaAcceptsAMinimalDefinition(): void
    {
        $this->assertValidAgainst('FilterDefinition', new FilterDefinition(key: 'status', label: 'Status'));
    }

    #[Test]
    public function testSchemaAcceptsAFullyPopulatedDefinition(): void
    {
        $this->assertValidAgainst('FilterDefinition', new FilterDefinition(
            key: 'status',
            label: Translatable::of('status', 'local_x'),
            type: FilterType::SELECT,
            options: [
                ['value' => 'active', 'label' => 'Active'],
                ['value' => 'archived', 'label' => Translatable::of('archived', 'local_x')],
            ],
            placeholder: 'Pick one',
            default: 'active',
        ));
    }

    #[Test]
    public function testSchemaRejectsADefinitionMissingItsKey(): void
    {
        $this->assertInvalidAgainst('FilterDefinition', ['label' => 'Status', 'type' => 'select']);
    }

    #[Test]
    public function testSchemaRejectsAnUnknownProperty(): void
    {
        $this->assertInvalidAgainst('FilterDefinition', ['key' => 'status', 'label' => 'Status', 'type' => 'select', 'unknown' => true]);
    }

    #[Test]
    public function testSchemaRejectsANonStringKey(): void
    {
        $this->assertInvalidAgainst('FilterDefinition', ['key' => 42, 'label' => 'Status', 'type' => 'select']);
    }
}

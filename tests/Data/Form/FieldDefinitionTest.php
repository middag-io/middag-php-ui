<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Tests\Data\Form;

use Middag\Ui\Data\Form\Condition;
use Middag\Ui\Data\Form\FieldDefinition;
use Middag\Ui\Enum\ConditionOperator;
use Middag\Ui\Enum\FieldType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @internal
 */
#[CoversClass(FieldDefinition::class)]
final class FieldDefinitionTest extends TestCase
{
    #[Test]
    public function testIsReadonlyClass(): void
    {
        $ref = new ReflectionClass(FieldDefinition::class);

        self::assertTrue($ref->isReadOnly());
    }

    #[Test]
    public function testConstructsWithScalarValues(): void
    {
        $def = new FieldDefinition(
            name: 'email',
            type: FieldType::EMAIL,
            label: ['key' => 'email_label', 'component' => ''],
            help: null,
            default: '',
            required: true,
            attributes: [],
            conditions: [],
            options: [],
        );

        self::assertSame('email', $def->name);
        self::assertSame(FieldType::EMAIL, $def->type);
        self::assertSame(['key' => 'email_label', 'component' => ''], $def->label);
        self::assertNull($def->help);
        self::assertSame('', $def->default);
        self::assertTrue($def->required);
        self::assertSame([], $def->attributes);
        self::assertSame([], $def->conditions);
        self::assertSame([], $def->options);
    }

    #[Test]
    public function testConditionsHoldConditionObjects(): void
    {
        $cond = new Condition(field: 'role', operator: ConditionOperator::EQ, value: 'admin');

        $def = new FieldDefinition(
            name: 'secret',
            type: FieldType::TEXT,
            label: null,
            help: null,
            default: null,
            required: false,
            attributes: [],
            conditions: [$cond],
            options: [],
        );

        self::assertCount(1, $def->conditions);
        self::assertSame($cond, $def->conditions[0]);
    }

    #[Test]
    public function testSelectFieldHoldsOptions(): void
    {
        $def = new FieldDefinition(
            name: 'status',
            type: FieldType::SELECT,
            label: null,
            help: null,
            default: 'active',
            required: false,
            attributes: [],
            conditions: [],
            options: ['active' => 'Active', 'inactive' => 'Inactive'],
        );

        self::assertSame(['active' => 'Active', 'inactive' => 'Inactive'], $def->options);
        self::assertSame('active', $def->default);
    }
}

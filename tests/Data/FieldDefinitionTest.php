<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Tests\Data;

use Middag\Ui\Data\Condition;
use Middag\Ui\Data\FieldConstraints;
use Middag\Ui\Data\FieldDefinition;
use Middag\Ui\Data\Translatable;
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
        $constraints = new FieldConstraints(required: true);

        $def = new FieldDefinition(
            name: 'email',
            type: FieldType::EMAIL,
            label: Translatable::of('email_label', 'local_x'),
            help: null,
            default: '',
            constraints: $constraints,
            attributes: [],
            conditions: [],
            options: [],
        );

        self::assertSame('email', $def->name);
        self::assertSame(FieldType::EMAIL, $def->type);
        self::assertInstanceOf(Translatable::class, $def->label);
        self::assertSame('email_label', $def->label->key);
        self::assertNull($def->help);
        self::assertSame('', $def->default);
        self::assertSame($constraints, $def->constraints);
        self::assertTrue($def->constraints->required);
        self::assertSame([], $def->attributes);
        self::assertSame([], $def->conditions);
        self::assertSame([], $def->options);
    }

    #[Test]
    public function testAcceptsRawStringLabel(): void
    {
        $def = new FieldDefinition(
            name: 'note',
            type: FieldType::TEXT,
            label: 'Note',
            help: 'Free text',
            default: null,
            constraints: new FieldConstraints(),
            attributes: [],
            conditions: [],
            options: [],
        );

        self::assertSame('Note', $def->label);
        self::assertSame('Free text', $def->help);
        self::assertFalse($def->constraints->required);
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
            constraints: new FieldConstraints(),
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
            constraints: new FieldConstraints(),
            attributes: [],
            conditions: [],
            options: ['active' => 'Active', 'inactive' => 'Inactive'],
        );

        self::assertSame(['active' => 'Active', 'inactive' => 'Inactive'], $def->options);
        self::assertSame('active', $def->default);
    }
}

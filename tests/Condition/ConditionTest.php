<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Tests\Condition;

use JsonSerializable;
use Middag\Ui\Condition\Condition;
use Middag\Ui\Shared\Enum\ConditionOperator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @internal
 */
#[CoversClass(Condition::class)]
final class ConditionTest extends TestCase
{
    #[Test]
    public function testIsNotJsonSerializable(): void
    {
        // Boundary object (ADR): renderers map it manually; it must not reach the wire.
        self::assertFalse(
            (new ReflectionClass(Condition::class))->implementsInterface(JsonSerializable::class),
        );
    }

    #[Test]
    public function testConstructWithAllFields(): void
    {
        $condition = new Condition(
            field: 'status',
            operator: ConditionOperator::Eq,
            value: 'active',
            kind: Condition::KIND_VISIBLE_WHEN,
        );

        self::assertSame('status', $condition->field);
        self::assertSame(ConditionOperator::Eq, $condition->operator);
        self::assertSame('active', $condition->value);
        self::assertSame(Condition::KIND_VISIBLE_WHEN, $condition->kind);
    }

    #[Test]
    public function testDefaultKindIsVisibleWhen(): void
    {
        $condition = new Condition(
            field: 'type',
            operator: ConditionOperator::Neq,
            value: 'draft',
        );

        self::assertSame(Condition::KIND_VISIBLE_WHEN, $condition->kind);
    }

    #[Test]
    public function testKindConstants(): void
    {
        self::assertSame('visible_when', Condition::KIND_VISIBLE_WHEN);
        self::assertSame('hidden_when', Condition::KIND_HIDDEN_WHEN);
        self::assertSame('required_when', Condition::KIND_REQUIRED_WHEN);
        self::assertSame('disabled_when', Condition::KIND_DISABLED_WHEN);
    }

    #[Test]
    public function testValueCanBeMixed(): void
    {
        $withBool = new Condition(field: 'enabled', operator: ConditionOperator::Truthy, value: true);
        $withInt = new Condition(field: 'count', operator: ConditionOperator::Gt, value: 5);
        $withArray = new Condition(field: 'role', operator: ConditionOperator::In, value: ['admin', 'editor']);

        self::assertTrue($withBool->value);
        self::assertSame(5, $withInt->value);
        self::assertSame(['admin', 'editor'], $withArray->value);
    }

    #[Test]
    public function testAllKindsCanBeSet(): void
    {
        foreach ([
            Condition::KIND_VISIBLE_WHEN,
            Condition::KIND_HIDDEN_WHEN,
            Condition::KIND_REQUIRED_WHEN,
            Condition::KIND_DISABLED_WHEN,
        ] as $kind) {
            $condition = new Condition(
                field: 'f',
                operator: ConditionOperator::Eq,
                value: 1,
                kind: $kind,
            );

            self::assertSame($kind, $condition->kind);
        }
    }
}

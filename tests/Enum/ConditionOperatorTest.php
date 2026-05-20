<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.com.br>
 * @copyright   2026 MIDDAG (https://www.middag.com.br)
 * @license     proprietary
 */

namespace Middag\Ui\Tests\Enum;

use Middag\Ui\Enum\ConditionOperator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class ConditionOperatorTest extends TestCase
{
    #[Test]
    public function allExpectedCasesExist(): void
    {
        $values = array_column(ConditionOperator::cases(), 'value');

        // Equality
        $this->assertContains('eq', $values);
        $this->assertContains('neq', $values);

        // Set membership
        $this->assertContains('in', $values);
        $this->assertContains('not_in', $values);

        // Numeric comparison
        $this->assertContains('gt', $values);
        $this->assertContains('gte', $values);
        $this->assertContains('lt', $values);
        $this->assertContains('lte', $values);

        // Boolean / presence
        $this->assertContains('truthy', $values);
        $this->assertContains('falsy', $values);
        $this->assertContains('exists', $values);
        $this->assertContains('empty', $values);

        // Pattern
        $this->assertContains('matches', $values);
    }

    #[Test]
    public function totalOperatorCount(): void
    {
        $this->assertCount(13, ConditionOperator::cases());
    }

    #[Test]
    public function enumValuesMatchExpectedStrings(): void
    {
        $this->assertSame('eq', ConditionOperator::EQ->value);
        $this->assertSame('neq', ConditionOperator::NEQ->value);
        $this->assertSame('in', ConditionOperator::IN->value);
        $this->assertSame('not_in', ConditionOperator::NOT_IN->value);
        $this->assertSame('gt', ConditionOperator::GT->value);
        $this->assertSame('gte', ConditionOperator::GTE->value);
        $this->assertSame('lt', ConditionOperator::LT->value);
        $this->assertSame('lte', ConditionOperator::LTE->value);
        $this->assertSame('truthy', ConditionOperator::TRUTHY->value);
        $this->assertSame('falsy', ConditionOperator::FALSY->value);
        $this->assertSame('exists', ConditionOperator::EXISTS->value);
        $this->assertSame('empty', ConditionOperator::EMPTY->value);
        $this->assertSame('matches', ConditionOperator::MATCHES->value);
    }

    #[Test]
    public function isMformCompatibleReturnsTrueForAllExceptMatches(): void
    {
        $compatibleOperators = [
            ConditionOperator::EQ,
            ConditionOperator::NEQ,
            ConditionOperator::IN,
            ConditionOperator::NOT_IN,
            ConditionOperator::GT,
            ConditionOperator::GTE,
            ConditionOperator::LT,
            ConditionOperator::LTE,
            ConditionOperator::TRUTHY,
            ConditionOperator::FALSY,
            ConditionOperator::EXISTS,
            ConditionOperator::EMPTY,
        ];

        foreach ($compatibleOperators as $operator) {
            $this->assertTrue(
                $operator->is_mform_compatible(),
                sprintf('Expected %s to be mform compatible', $operator->name),
            );
        }
    }

    #[Test]
    public function isMformCompatibleReturnsFalseForMatches(): void
    {
        $this->assertFalse(ConditionOperator::MATCHES->is_mform_compatible());
    }

    #[Test]
    public function onlyMatchesIsNotMformCompatible(): void
    {
        $incompatible = array_filter(
            ConditionOperator::cases(),
            fn (ConditionOperator $op): bool => !$op->is_mform_compatible(),
        );

        $this->assertCount(1, $incompatible);
        $this->assertSame(ConditionOperator::MATCHES, array_values($incompatible)[0]);
    }

    #[Test]
    public function canBeCreatedFromString(): void
    {
        $this->assertSame(ConditionOperator::EQ, ConditionOperator::from('eq'));
        $this->assertSame(ConditionOperator::MATCHES, ConditionOperator::from('matches'));
    }

    #[Test]
    public function tryFromReturnsNullForUnknown(): void
    {
        $this->assertNull(ConditionOperator::tryFrom('unknown'));
        $this->assertNull(ConditionOperator::tryFrom(''));
    }
}

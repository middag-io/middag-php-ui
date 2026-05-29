<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Tests\Enum;

use Middag\Ui\Enum\FieldType;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversNothing]
final class FieldTypeTest extends TestCase
{
    #[Test]
    public function allExpectedCasesExist(): void
    {
        $values = array_column(FieldType::cases(), 'value');

        $this->assertContains('text', $values);
        $this->assertContains('textarea', $values);
        $this->assertContains('richtext', $values);
        $this->assertContains('password', $values);
        $this->assertContains('email', $values);
        $this->assertContains('url', $values);
        $this->assertContains('int', $values);
        $this->assertContains('float', $values);
        $this->assertContains('select', $values);
        $this->assertContains('multiselect', $values);
        $this->assertContains('radio', $values);
        $this->assertContains('checkbox', $values);
        $this->assertContains('switch', $values);
        $this->assertContains('date', $values);
        $this->assertContains('datetime', $values);
        $this->assertContains('time', $values);
        $this->assertContains('duration', $values);
        $this->assertContains('file', $values);
        $this->assertContains('entity_picker', $values);
        $this->assertContains('autocomplete', $values);
        $this->assertContains('tags', $values);
        $this->assertContains('hidden', $values);
        $this->assertContains('static', $values);
        $this->assertContains('header', $values);
    }

    #[Test]
    public function totalFieldTypeCount(): void
    {
        $this->assertCount(24, FieldType::cases());
    }

    #[Test]
    public function enumValuesMatchExpectedStrings(): void
    {
        $this->assertSame('text', FieldType::TEXT->value);
        $this->assertSame('textarea', FieldType::TEXTAREA->value);
        $this->assertSame('richtext', FieldType::RICHTEXT->value);
        $this->assertSame('password', FieldType::PASSWORD->value);
        $this->assertSame('email', FieldType::EMAIL->value);
        $this->assertSame('url', FieldType::URL->value);
        $this->assertSame('int', FieldType::INT->value);
        $this->assertSame('float', FieldType::FLOAT->value);
        $this->assertSame('select', FieldType::SELECT->value);
        $this->assertSame('multiselect', FieldType::MULTISELECT->value);
        $this->assertSame('radio', FieldType::RADIO->value);
        $this->assertSame('checkbox', FieldType::CHECKBOX->value);
        $this->assertSame('switch', FieldType::SWITCH->value);
        $this->assertSame('date', FieldType::DATE->value);
        $this->assertSame('datetime', FieldType::DATETIME->value);
        $this->assertSame('time', FieldType::TIME->value);
        $this->assertSame('duration', FieldType::DURATION->value);
        $this->assertSame('file', FieldType::FILE->value);
        $this->assertSame('entity_picker', FieldType::ENTITY_PICKER->value);
        $this->assertSame('autocomplete', FieldType::AUTOCOMPLETE->value);
        $this->assertSame('tags', FieldType::TAGS->value);
        $this->assertSame('hidden', FieldType::HIDDEN->value);
        $this->assertSame('static', FieldType::STATIC->value);
        $this->assertSame('header', FieldType::HEADER->value);
    }

    #[Test]
    public function fieldTypeCanBeCreatedFromString(): void
    {
        $type = FieldType::from('text');
        $this->assertSame(FieldType::TEXT, $type);

        $type = FieldType::from('entity_picker');
        $this->assertSame(FieldType::ENTITY_PICKER, $type);
    }

    #[Test]
    public function fieldTypeTryFromReturnsNullForUnknown(): void
    {
        $type = FieldType::tryFrom('nonexistent');
        $this->assertNull($type);
    }

    #[Test]
    public function fieldTypeTryFromReturnsNullForEmptyString(): void
    {
        $type = FieldType::tryFrom('');
        $this->assertNull($type);
    }

    #[Test]
    public function allValuesAreLowercaseSnakeCase(): void
    {
        foreach (FieldType::cases() as $case) {
            $this->assertMatchesRegularExpression(
                '/^[a-z][a-z0-9_]*$/',
                $case->value,
                sprintf('FieldType::%s value "%s" is not lowercase snake_case', $case->name, $case->value),
            );
        }
    }
}

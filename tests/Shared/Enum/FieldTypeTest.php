<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Tests\Shared\Enum;

use Middag\Ui\Shared\Enum\FieldType;
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
        $this->assertContains('slider', $values);
        $this->assertContains('otp', $values);
        $this->assertContains('native_select', $values);
    }

    #[Test]
    public function totalFieldTypeCount(): void
    {
        $this->assertCount(27, FieldType::cases());
    }

    #[Test]
    public function enumValuesMatchExpectedStrings(): void
    {
        $this->assertSame('text', FieldType::Text->value);
        $this->assertSame('textarea', FieldType::Textarea->value);
        $this->assertSame('richtext', FieldType::Richtext->value);
        $this->assertSame('password', FieldType::Password->value);
        $this->assertSame('email', FieldType::Email->value);
        $this->assertSame('url', FieldType::Url->value);
        $this->assertSame('int', FieldType::Int->value);
        $this->assertSame('float', FieldType::Float->value);
        $this->assertSame('select', FieldType::Select->value);
        $this->assertSame('multiselect', FieldType::Multiselect->value);
        $this->assertSame('radio', FieldType::Radio->value);
        $this->assertSame('checkbox', FieldType::Checkbox->value);
        $this->assertSame('switch', FieldType::Switch->value);
        $this->assertSame('date', FieldType::Date->value);
        $this->assertSame('datetime', FieldType::Datetime->value);
        $this->assertSame('time', FieldType::Time->value);
        $this->assertSame('duration', FieldType::Duration->value);
        $this->assertSame('file', FieldType::File->value);
        $this->assertSame('entity_picker', FieldType::EntityPicker->value);
        $this->assertSame('autocomplete', FieldType::Autocomplete->value);
        $this->assertSame('tags', FieldType::Tags->value);
        $this->assertSame('hidden', FieldType::Hidden->value);
        $this->assertSame('static', FieldType::Static->value);
        $this->assertSame('header', FieldType::Header->value);
        $this->assertSame('slider', FieldType::Slider->value);
        $this->assertSame('otp', FieldType::Otp->value);
        $this->assertSame('native_select', FieldType::NativeSelect->value);
    }

    #[Test]
    public function fieldTypeCanBeCreatedFromString(): void
    {
        $type = FieldType::from('text');
        $this->assertSame(FieldType::Text, $type);

        $type = FieldType::from('entity_picker');
        $this->assertSame(FieldType::EntityPicker, $type);
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

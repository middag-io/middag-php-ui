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

use Middag\Ui\Shared\Enum\FormComponent;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversNothing]
final class FormComponentTest extends TestCase
{
    /**
     * The wire renderer catalog mirrored from @middag-io/react's FormFieldType.
     */
    private const EXPECTED = [
        'text', 'textarea', 'password', 'otp', 'email', 'url', 'int', 'float',
        'slider', 'select', 'native_select', 'multiselect', 'radio', 'checkbox',
        'switch', 'date', 'datetime', 'duration', 'file', 'entity_picker',
        'phone', 'document', 'currency', 'color', 'slug', 'tags', 'rating',
        'date_range', 'hidden', 'static', 'header',
    ];

    #[Test]
    public function allExpectedCasesExist(): void
    {
        $values = array_column(FormComponent::cases(), 'value');

        foreach (self::EXPECTED as $value) {
            $this->assertContains($value, $values);
        }
    }

    #[Test]
    public function totalComponentCount(): void
    {
        $this->assertCount(31, FormComponent::cases());
    }

    #[Test]
    public function enumValuesMatchExpectedStrings(): void
    {
        $this->assertSame('text', FormComponent::TEXT->value);
        $this->assertSame('native_select', FormComponent::NATIVE_SELECT->value);
        $this->assertSame('entity_picker', FormComponent::ENTITY_PICKER->value);
        $this->assertSame('date_range', FormComponent::DATE_RANGE->value);
        $this->assertSame('header', FormComponent::HEADER->value);
    }

    #[Test]
    public function componentCanBeCreatedFromString(): void
    {
        $this->assertSame(FormComponent::DATE_RANGE, FormComponent::from('date_range'));
    }

    #[Test]
    public function tryFromReturnsNullForUnknown(): void
    {
        $this->assertNull(FormComponent::tryFrom('nonexistent'));
    }

    #[Test]
    public function allValuesAreLowercaseSnakeCase(): void
    {
        foreach (FormComponent::cases() as $case) {
            $this->assertMatchesRegularExpression(
                '/^[a-z][a-z0-9_]*$/',
                $case->value,
                sprintf('FormComponent::%s value "%s" is not lowercase snake_case', $case->name, $case->value),
            );
        }
    }

    #[Test]
    public function jsonSchemaEnumeratesEveryCase(): void
    {
        $schema = FormComponent::jsonSchema();

        $this->assertSame('string', $schema['type']);
        $this->assertSame(array_column(FormComponent::cases(), 'value'), $schema['enum']);
    }
}

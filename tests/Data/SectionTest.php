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

use Middag\Ui\Contract\FieldInterface;
use Middag\Ui\Contract\LayoutElementInterface;
use Middag\Ui\Data\Section;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @internal
 */
#[CoversClass(Section::class)]
final class SectionTest extends TestCase
{
    #[Test]
    public function testIsReadonlyClass(): void
    {
        self::assertTrue((new ReflectionClass(Section::class))->isReadOnly());
    }

    #[Test]
    public function testIdReturnedFromFactory(): void
    {
        $section = Section::of('personal');

        self::assertSame('personal', $section->id());
    }

    #[Test]
    public function testLabelNullByDefault(): void
    {
        $section = Section::of('s');

        self::assertNull($section->labelData());
    }

    #[Test]
    public function testLabelSetsKeyAndComponent(): void
    {
        $section = Section::of('s')->label('section_title', 'MyComponent');

        self::assertSame(['key' => 'section_title', 'component' => 'MyComponent'], $section->labelData());
    }

    #[Test]
    public function testLabelDefaultsComponentToEmptyString(): void
    {
        $section = Section::of('s')->label('section_title');

        self::assertSame(['key' => 'section_title', 'component' => ''], $section->labelData());
    }

    #[Test]
    public function testChildrenEmptyByDefault(): void
    {
        $section = Section::of('s');

        self::assertSame([], $section->children());
    }

    #[Test]
    public function testFieldsReplacesChildren(): void
    {
        $field = $this->createStub(FieldInterface::class);

        $section = Section::of('s')->fields($field);

        self::assertCount(1, $section->children());
        self::assertSame($field, $section->children()[0]);
    }

    #[Test]
    public function testFieldsAcceptsMultiple(): void
    {
        $f1 = $this->createStub(FieldInterface::class);
        $f2 = $this->createStub(FieldInterface::class);
        $f3 = $this->createStub(FieldInterface::class);

        $section = Section::of('s')->fields($f1, $f2, $f3);

        self::assertCount(3, $section->children());
    }

    #[Test]
    public function testImplementsLayoutElementInterface(): void
    {
        self::assertInstanceOf(LayoutElementInterface::class, Section::of('s'));
    }

    #[Test]
    public function testWithersAreImmutableAndChainable(): void
    {
        $field = $this->createStub(FieldInterface::class);
        $section = Section::of('s');

        $labelled = $section->label('key');
        $withFields = $labelled->fields($field);

        // Each wither returns a new instance; the original stays untouched.
        self::assertNotSame($section, $labelled);
        self::assertNotSame($labelled, $withFields);
        self::assertNull($section->labelData());
        self::assertSame([], $section->children());

        // The fully-built section carries both the label and the fields.
        self::assertSame(['key' => 'key', 'component' => ''], $withFields->labelData());
        self::assertCount(1, $withFields->children());
    }
}

<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.com.br>
 * @copyright   2026 MIDDAG (https://www.middag.com.br)
 * @license     proprietary
 */

namespace Middag\Ui\Tests\Infrastructure\Form\Layout;

use Middag\Ui\Contract\Form\FieldInterface;
use Middag\Ui\Contract\Form\LayoutElementInterface;
use Middag\Ui\Infrastructure\Form\Layout\Section;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(Section::class)]
final class SectionTest extends TestCase
{
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
        $section = Section::of('s');
        $section->label('section_title', 'MyComponent');

        self::assertSame(['key' => 'section_title', 'component' => 'MyComponent'], $section->labelData());
    }

    #[Test]
    public function testLabelDefaultsComponentToEmptyString(): void
    {
        $section = Section::of('s');
        $section->label('section_title');

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

        $section = Section::of('s');
        $section->fields($field);

        self::assertCount(1, $section->children());
        self::assertSame($field, $section->children()[0]);
    }

    #[Test]
    public function testFieldsAcceptsMultiple(): void
    {
        $f1 = $this->createStub(FieldInterface::class);
        $f2 = $this->createStub(FieldInterface::class);
        $f3 = $this->createStub(FieldInterface::class);

        $section = Section::of('s');
        $section->fields($f1, $f2, $f3);

        self::assertCount(3, $section->children());
    }

    #[Test]
    public function testImplementsLayoutElementInterface(): void
    {
        self::assertInstanceOf(LayoutElementInterface::class, Section::of('s'));
    }

    #[Test]
    public function testFluentChain(): void
    {
        $section = Section::of('s');
        $result = $section->label('key');

        self::assertSame($section, $result);

        $field = $this->createStub(FieldInterface::class);
        $result2 = $section->fields($field);

        self::assertSame($section, $result2);
    }
}

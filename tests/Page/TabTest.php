<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Tests\Page;

use Middag\Ui\Block\BlockDescriptor;
use Middag\Ui\Block\Contract\BlockDescriptorInterface;
use Middag\Ui\Page\Tab;
use Middag\Ui\Shared\ValueObject\Translatable;
use Middag\Ui\Tests\Support\ValidatesAgainstSchema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @internal
 */
#[CoversClass(Tab::class)]
final class TabTest extends TestCase
{
    use ValidatesAgainstSchema;

    #[Test]
    public function testIsReadonlyClass(): void
    {
        self::assertTrue((new ReflectionClass(Tab::class))->isReadOnly());
    }

    #[Test]
    public function testSerializesLiteralLabelAndEmptyBlocks(): void
    {
        $tab = new Tab('overview', 'Overview');

        self::assertSame(
            ['id' => 'overview', 'label' => 'Overview', 'blocks' => []],
            $tab->jsonSerialize(),
        );
    }

    #[Test]
    public function testSerializesTranslatableLabel(): void
    {
        $tab = new Tab('overview', Translatable::of('tab_overview', 'forms'));

        $payload = $tab->jsonSerialize();

        self::assertSame(['key' => 'tab_overview', 'domain' => 'forms'], $payload['label']);
    }

    #[Test]
    public function testSerializesNestedBlocks(): void
    {
        $block = $this->createMock(BlockDescriptorInterface::class);
        $block->method('jsonSerialize')->willReturn(['type' => 'markdown_panel', 'key' => 'm']);

        $tab = new Tab('overview', 'Overview', [$block]);

        $payload = $tab->jsonSerialize();

        self::assertSame([['type' => 'markdown_panel', 'key' => 'm']], $payload['blocks']);
    }

    #[Test]
    public function testSchemaAcceptsALiteralLabelTab(): void
    {
        $this->assertValidAgainst('Tab', new Tab('overview', 'Overview'));
    }

    #[Test]
    public function testSchemaAcceptsATranslatableLabelTabWithBlocks(): void
    {
        $this->assertValidAgainst('Tab', new Tab(
            'overview',
            Translatable::of('tab_overview', 'forms'),
            [new BlockDescriptor(type: 'markdown_panel', key: 'm', data: ['body' => 'hi'])],
        ));
    }

    #[Test]
    public function testSchemaRejectsATabMissingItsBlocks(): void
    {
        $this->assertInvalidAgainst('Tab', ['id' => 'overview', 'label' => 'Overview']);
    }

    #[Test]
    public function testSchemaRejectsAnUnknownTabProperty(): void
    {
        $this->assertInvalidAgainst('Tab', ['id' => 'overview', 'label' => 'Overview', 'blocks' => [], 'extra' => 1]);
    }

    #[Test]
    public function testSchemaRejectsANonStringTabId(): void
    {
        $this->assertInvalidAgainst('Tab', ['id' => 5, 'label' => 'Overview', 'blocks' => []]);
    }
}

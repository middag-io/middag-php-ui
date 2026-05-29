<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Tests;

use Middag\Ui\AbstractWidget;
use Middag\Ui\Contract\WidgetInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(AbstractWidget::class)]
final class AbstractWidgetTest extends TestCase
{
    #[Test]
    public function testConcreteWidgetSatisfiesContract(): void
    {
        $widget = new class extends AbstractWidget {
            private string $id = '';

            public function setId(string $id): void
            {
                $this->id = $id;
            }

            public function getId(): string
            {
                return $this->id;
            }

            /** @return array<string, mixed> */
            public function getData(): array
            {
                return ['id' => $this->id];
            }

            public function render(): string
            {
                return '<div>' . $this->id . '</div>';
            }
        };

        $widget->setId('w1');

        self::assertInstanceOf(WidgetInterface::class, $widget);
        self::assertSame('w1', $widget->getId());
        self::assertSame(['id' => 'w1'], $widget->getData());
        self::assertSame('<div>w1</div>', $widget->render());
    }
}

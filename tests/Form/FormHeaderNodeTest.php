<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Tests\Form;

use Middag\Ui\Form\FormHeaderNode;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(FormHeaderNode::class)]
final class FormHeaderNodeTest extends TestCase
{
    #[Test]
    public function testSerializesHeaderShape(): void
    {
        $payload = (new FormHeaderNode('Contact details'))->jsonSerialize();

        self::assertSame(['kind' => 'header', 'label' => 'Contact details'], $payload);
    }
}

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

use Middag\Ui\Form\FormFieldNode;
use Middag\Ui\Form\FormGroupNode;
use Middag\Ui\Shared\Enum\FormComponent;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(FormGroupNode::class)]
final class FormGroupNodeTest extends TestCase
{
    #[Test]
    public function testSerializesWithColumns(): void
    {
        $payload = (new FormGroupNode(
            id: 'name_row',
            children: [new FormFieldNode('first', FormComponent::TEXT, ['label' => 'First'])],
            columns: 2,
        ))->jsonSerialize();

        self::assertSame('group', $payload['kind']);
        self::assertSame('name_row', $payload['id']);
        self::assertSame(2, $payload['columns']);
        self::assertCount(1, $payload['children']);
    }

    #[Test]
    public function testOmitsColumnsWhenNull(): void
    {
        $payload = (new FormGroupNode(id: 'g'))->jsonSerialize();

        self::assertArrayNotHasKey('columns', $payload);
        self::assertSame([], $payload['children']);
    }
}

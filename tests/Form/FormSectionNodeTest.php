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
use Middag\Ui\Form\FormSectionNode;
use Middag\Ui\Shared\Enum\FormComponent;
use Middag\Ui\Shared\ValueObject\Translatable;
use Middag\Ui\Tests\Support\ValidatesAgainstSchema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(FormSectionNode::class)]
final class FormSectionNodeTest extends TestCase
{
    use ValidatesAgainstSchema;

    #[Test]
    public function testSerializesWithChildrenAndCollapsed(): void
    {
        $payload = (new FormSectionNode(
            id: 'advanced',
            label: 'Advanced',
            children: [new FormFieldNode('secret', FormComponent::Password, ['label' => 'Secret'])],
            defaultCollapsed: true,
        ))->jsonSerialize();

        self::assertSame('section', $payload['kind']);
        self::assertSame('advanced', $payload['id']);
        self::assertSame('Advanced', $payload['label']);
        self::assertTrue($payload['defaultCollapsed']);
        self::assertIsArray($payload['children']);
        self::assertCount(1, $payload['children']);
        self::assertSame('field', $payload['children'][0]['kind']);
    }

    #[Test]
    public function testOmitsCollapsedWhenFalse(): void
    {
        $payload = (new FormSectionNode(id: 's', label: 'S'))->jsonSerialize();

        self::assertArrayNotHasKey('defaultCollapsed', $payload);
        self::assertSame([], $payload['children']);
    }

    #[Test]
    public function testSchemaAcceptsACollapsedSectionWithChildren(): void
    {
        $this->assertValidAgainst('FormSectionNode', new FormSectionNode(
            id: 'advanced',
            label: 'Advanced',
            children: [new FormFieldNode('secret', FormComponent::Password, ['label' => 'Secret'])],
            defaultCollapsed: true,
        ));
    }

    #[Test]
    public function testExistingStringSectionShapeIsByteIdentical(): void
    {
        $json = json_encode(new FormSectionNode(
            id: 'advanced',
            label: 'Advanced',
            children: [new FormFieldNode('secret', FormComponent::Password, ['label' => 'Secret'])],
            defaultCollapsed: true,
        ), JSON_THROW_ON_ERROR);

        self::assertSame(
            '{"kind":"section","id":"advanced","label":"Advanced","children":[{"kind":"field","key":"secret","component":"password","props":{"label":"Secret"}}],"defaultCollapsed":true}',
            $json,
        );
    }

    #[Test]
    public function testTranslatableLabelSerializesAsKeyAndDomain(): void
    {
        $json = json_encode(new FormSectionNode('s', Translatable::of('section.title', 'local_x')), JSON_THROW_ON_ERROR);

        self::assertSame('{"kind":"section","id":"s","label":{"key":"section.title","domain":"local_x"},"children":[]}', $json);
    }

    #[Test]
    public function testTranslatableLabelKeepsParams(): void
    {
        $payload = (new FormSectionNode('s', Translatable::of('k', 'd', ['n' => 2])))->jsonSerialize();

        self::assertSame(['key' => 'k', 'domain' => 'd', 'params' => ['n' => 2]], $payload['label']);
    }

    #[Test]
    public function testNullLabelSerializesAsExplicitNull(): void
    {
        $json = json_encode(new FormSectionNode('s', null), JSON_THROW_ON_ERROR);

        self::assertSame('{"kind":"section","id":"s","label":null,"children":[]}', $json);
    }

    #[Test]
    public function testSchemaAcceptsTranslatableAndNullLabels(): void
    {
        $this->assertValidAgainst('FormSectionNode', new FormSectionNode('s', Translatable::of('k', 'd')));
        $this->assertValidAgainst('FormSectionNode', new FormSectionNode('s', null));
    }

    #[Test]
    public function testSchemaAcceptsAMinimalSection(): void
    {
        $this->assertValidAgainst('FormSectionNode', new FormSectionNode(id: 's', label: 'S'));
    }

    #[Test]
    public function testSchemaRejectsASectionMissingItsLabel(): void
    {
        $this->assertInvalidAgainst('FormSectionNode', ['kind' => 'section', 'id' => 's', 'children' => []]);
    }

    #[Test]
    public function testSchemaRejectsASectionWithAWrongKind(): void
    {
        $this->assertInvalidAgainst('FormSectionNode', ['kind' => 'group', 'id' => 's', 'label' => 'S', 'children' => []]);
    }

    #[Test]
    public function testSchemaRejectsANonBooleanDefaultCollapsed(): void
    {
        $this->assertInvalidAgainst('FormSectionNode', ['kind' => 'section', 'id' => 's', 'label' => 'S', 'children' => [], 'defaultCollapsed' => 'yes']);
    }

    #[Test]
    public function testSchemaRejectsAnAdditionalPropertyOnASection(): void
    {
        $this->assertInvalidAgainst('FormSectionNode', ['kind' => 'section', 'id' => 's', 'label' => 'S', 'children' => [], 'evil' => 1]);
    }
}

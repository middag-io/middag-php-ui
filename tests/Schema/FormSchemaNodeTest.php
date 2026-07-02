<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Tests\Schema;

use JsonSerializable;
use Middag\Ui\Form\FormFieldNode;
use Middag\Ui\Form\FormGroupNode;
use Middag\Ui\Form\FormHeaderNode;
use Middag\Ui\Form\FormSectionNode;
use Middag\Ui\Schema\SchemaRegistry;
use Middag\Ui\Shared\Enum\FormComponent;
use Opis\JsonSchema\Errors\ErrorFormatter;
use Opis\JsonSchema\Errors\ValidationError;
use Opis\JsonSchema\Validator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Proves the serialized form schema tree validates against the hand-authored
 * jsonSchema() of the FormSchemaNode island — so the canonical form-field union
 * (the codegen source of truth) can never drift from the wire VOs. Validates
 * with opis/json-schema (draft 2020-12), same as {@see SchemaRoundtripTest}.
 *
 * @internal
 */
#[CoversClass(SchemaRegistry::class)]
final class FormSchemaNodeTest extends TestCase
{
    /**
     * Every FormComponent in the catalog must validate against the FormFieldNode
     * union with minimal props. Because the union is a `oneOf`, a passing
     * validation also proves the branches are DISJOINT (opis fails oneOf when a
     * payload matches zero OR more than one branch) and EXHAUSTIVE (every
     * catalog value has a branch) — the partition invariant, in one assertion.
     */
    #[Test]
    public function testEveryComponentValidatesAgainstTheFieldNodeUnion(): void
    {
        foreach (FormComponent::cases() as $component) {
            $node = new FormFieldNode('f_' . $component->value, $component, ['label' => 'Test']);
            $this->assertValidAgainst('FormFieldNode', $node);
        }
    }

    #[Test]
    public function testRichComponentPropsValidate(): void
    {
        // select — option props.
        $this->assertValidAgainst('FormFieldNode', new FormFieldNode('type', FormComponent::SELECT, [
            'label' => 'Type',
            'options' => [['value' => 'a', 'label' => 'A'], ['value' => 'b', 'label' => 'B']],
        ]));
        // document — base + scope enum + masks map ($ref FormFieldDocumentMask).
        $this->assertValidAgainst('FormFieldNode', new FormFieldNode('doc', FormComponent::DOCUMENT, [
            'label' => 'Document',
            'documentScope' => 'person',
            'documentMasks' => ['cpf' => ['pattern' => '999.999.999-99', 'maxLength' => 14]],
        ]));
        // slider — numeric bounds + range toggle.
        $this->assertValidAgainst('FormFieldNode', new FormFieldNode('score', FormComponent::SLIDER, [
            'label' => 'Score',
            'min' => 0,
            'max' => 10,
            'multiple' => true,
        ]));
        // entity_picker — option props + async search props (nested allOf).
        $this->assertValidAgainst('FormFieldNode', new FormFieldNode('owner', FormComponent::ENTITY_PICKER, [
            'label' => 'Owner',
            'options' => [['value' => '1', 'label' => 'Ana']],
            'autocompleteHref' => '/api/users',
            'autocompleteMinChars' => 2,
        ]));
        // a field carrying a reactive condition ($ref FormCondition + validation).
        $this->assertValidAgainst('FormFieldNode', new FormFieldNode('secret', FormComponent::PASSWORD, [
            'label' => 'Secret',
            'validation' => ['minLength' => 8],
            'visible_when' => ['field' => 'type', 'operator' => 'equals', 'value' => 'b'],
        ]));
    }

    /**
     * The FormSchemaNode umbrella accepts every node kind, and section/group
     * children recurse through the same $def (exercises the recursive $ref the
     * codegen turns into a recursive TypeScript type).
     */
    #[Test]
    public function testUmbrellaAcceptsEveryNodeKindAndRecurses(): void
    {
        $this->assertValidAgainst('FormSchemaNode', new FormFieldNode('a', FormComponent::TEXT, ['label' => 'A']));
        $this->assertValidAgainst('FormSchemaNode', new FormHeaderNode('Header'));
        $this->assertValidAgainst('FormSchemaNode', new FormGroupNode('g', [
            new FormFieldNode('a', FormComponent::TEXT, ['label' => 'A']),
        ], 2));

        $tree = new FormSectionNode('sec', 'Section', [
            new FormHeaderNode('Sub header'),
            new FormGroupNode('row', [
                new FormFieldNode('first', FormComponent::TEXT, ['label' => 'First']),
                new FormFieldNode('last', FormComponent::TEXT, ['label' => 'Last']),
            ], 2),
            new FormFieldNode('bio', FormComponent::TEXTAREA, ['label' => 'Bio', 'rows' => 4]),
        ], true);
        $this->assertValidAgainst('FormSchemaNode', $tree);
    }

    /**
     * Strictness guards — each mirrors a closed-branch invariant a happy-path
     * roundtrip cannot catch (the over-loosening regressions).
     */
    #[Test]
    public function testStrictnessIsEnforced(): void
    {
        // Sanity: a well-formed field validates, so the rejections are meaningful.
        $this->assertValidAgainst('FormFieldNode', ['kind' => 'field', 'key' => 'x', 'component' => 'text', 'props' => ['label' => 'X']]);

        // Unknown component: no oneOf branch has that discriminant.
        $this->assertInvalidAgainst('FormFieldNode', ['kind' => 'field', 'key' => 'x', 'component' => 'bogus', 'props' => ['label' => 'X']]);
        // additionalProperties:false at the node level: an extra top-level key.
        $this->assertInvalidAgainst('FormFieldNode', ['kind' => 'field', 'key' => 'x', 'component' => 'text', 'props' => ['label' => 'X'], 'evil' => 1]);
        // Missing the required identity `key`.
        $this->assertInvalidAgainst('FormFieldNode', ['kind' => 'field', 'component' => 'text', 'props' => ['label' => 'X']]);
        // props missing the required `label` (FieldPropsBase).
        $this->assertInvalidAgainst('FormFieldNode', ['kind' => 'field', 'key' => 'x', 'component' => 'text', 'props' => ['placeholder' => 'hi']]);
        // Unknown node kind for the umbrella union.
        $this->assertInvalidAgainst('FormSchemaNode', ['kind' => 'bogus']);
    }

    /**
     * Validate a serialized value against one registered $def, with the full
     * registry available so internal $refs resolve.
     *
     * @param array<string, mixed>|JsonSerializable $value
     */
    private function assertValidAgainst(string $defName, array|JsonSerializable $value): void
    {
        $serialized = $value instanceof JsonSerializable ? $value->jsonSerialize() : $value;

        $data = json_decode(json_encode($serialized) ?: 'null');
        $schema = json_decode(json_encode([
            '$ref' => '#/$defs/' . $defName,
            '$defs' => SchemaRegistry::defs(),
        ]) ?: 'null');

        $result = (new Validator())->validate($data, $schema);

        if (!$result->isValid()) {
            $error = $result->error();
            $formatted = $error instanceof ValidationError ? (new ErrorFormatter())->format($error) : [];
            self::fail($defName . ' serialize() did not validate against its jsonSchema():
'
                . json_encode($formatted, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }

        self::assertTrue($result->isValid());
    }

    /**
     * Assert a raw payload is REJECTED by a registered $def.
     *
     * @param array<string, mixed> $value
     */
    private function assertInvalidAgainst(string $defName, array $value): void
    {
        $data = json_decode(json_encode($value) ?: 'null');
        $schema = json_decode(json_encode([
            '$ref' => '#/$defs/' . $defName,
            '$defs' => SchemaRegistry::defs(),
        ]) ?: 'null');

        $result = (new Validator())->validate($data, $schema);

        self::assertFalse(
            $result->isValid(),
            $defName . ' must reject an invalid payload (branch strictness over-loosened?).',
        );
    }
}

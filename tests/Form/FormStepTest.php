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

use Middag\Ui\Form\FormStep;
use Middag\Ui\Shared\ValueObject\Translatable;
use Middag\Ui\Tests\Support\ValidatesAgainstSchema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @internal
 */
#[CoversClass(FormStep::class)]
final class FormStepTest extends TestCase
{
    use ValidatesAgainstSchema;

    #[Test]
    public function testIsReadonlyClass(): void
    {
        self::assertTrue((new ReflectionClass(FormStep::class))->isReadOnly());
    }

    #[Test]
    public function testSerializesMinimal(): void
    {
        $payload = (new FormStep(id: 'basics', label: 'Basics', fields: ['name', 'email']))->jsonSerialize();

        self::assertSame([
            'id' => 'basics',
            'label' => 'Basics',
            'fields' => ['name', 'email'],
        ], $payload);
        self::assertArrayNotHasKey('help', $payload);
    }

    #[Test]
    public function testSerializesHelpAndTranslatableLabel(): void
    {
        $payload = (new FormStep(
            id: 'basics',
            label: Translatable::of('step_basics', 'local_x'),
            fields: [],
            help: Translatable::of('step_help', 'local_x'),
        ))->jsonSerialize();

        self::assertSame(['key' => 'step_basics', 'domain' => 'local_x'], $payload['label']);
        self::assertSame(['key' => 'step_help', 'domain' => 'local_x'], $payload['help']);
    }

    #[Test]
    public function testSchemaAcceptsAStepWithAPlainStringLabel(): void
    {
        $this->assertValidAgainst('FormStep', new FormStep(id: 'basics', label: 'Basics', fields: ['name', 'email']));
    }

    #[Test]
    public function testSchemaAcceptsAStepWithTranslatableLabelAndHelp(): void
    {
        $this->assertValidAgainst('FormStep', new FormStep(
            id: 'basics',
            label: Translatable::of('step_basics', 'local_x'),
            fields: [],
            help: Translatable::of('step_help', 'local_x'),
        ));
    }

    #[Test]
    public function testSchemaRejectsAStepMissingItsFields(): void
    {
        $this->assertInvalidAgainst('FormStep', ['id' => 'x', 'label' => 'X']);
    }

    #[Test]
    public function testSchemaRejectsNonStringFieldEntries(): void
    {
        $this->assertInvalidAgainst('FormStep', ['id' => 'x', 'label' => 'X', 'fields' => [123]]);
    }

    #[Test]
    public function testSchemaRejectsAnAdditionalPropertyOnAStep(): void
    {
        $this->assertInvalidAgainst('FormStep', ['id' => 'x', 'label' => 'X', 'fields' => [], 'evil' => 1]);
    }
}

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

use Middag\Ui\Form\FormState;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @internal
 */
#[CoversClass(FormState::class)]
final class FormStateTest extends TestCase
{
    #[Test]
    public function testIsReadonlyClass(): void
    {
        self::assertTrue((new ReflectionClass(FormState::class))->isReadOnly());
    }

    #[Test]
    public function testInitialStateIsEmpty(): void
    {
        $state = new FormState();

        self::assertSame([], $state->values());
        self::assertSame([], $state->errors());
        self::assertFalse($state->isSubmitted());
    }

    #[Test]
    public function testWithValuesUpdatesValues(): void
    {
        $state = new FormState();
        $next = $state->withValues(['name' => 'Alice', 'email' => 'alice@example.com']);

        self::assertSame(['name' => 'Alice', 'email' => 'alice@example.com'], $next->values());
    }

    #[Test]
    public function testWithValuesMarksSubmitted(): void
    {
        $state = new FormState();
        $next = $state->withValues(['name' => 'Alice']);

        self::assertTrue($next->isSubmitted());
    }

    #[Test]
    public function testWithErrorsUpdatesErrors(): void
    {
        $state = new FormState();
        $next = $state->withErrors(['email' => 'Invalid email']);

        self::assertSame(['email' => 'Invalid email'], $next->errors());
    }

    #[Test]
    public function testWithValuesIsImmutable(): void
    {
        $original = new FormState();
        $original->withValues(['name' => 'Alice']);

        self::assertSame([], $original->values());
        self::assertFalse($original->isSubmitted());
    }

    #[Test]
    public function testWithErrorsIsImmutable(): void
    {
        $original = new FormState();
        $original->withErrors(['email' => 'Invalid']);

        self::assertSame([], $original->errors());
    }

    #[Test]
    public function testWithValuesDoesNotClearExistingErrors(): void
    {
        $state = (new FormState())->withErrors(['email' => 'Invalid']);
        $next = $state->withValues(['name' => 'Alice']);

        self::assertSame(['email' => 'Invalid'], $next->errors());
        self::assertSame(['name' => 'Alice'], $next->values());
    }

    #[Test]
    public function testWithErrorsDoesNotClearValues(): void
    {
        $state = (new FormState())->withValues(['name' => 'Alice']);
        $next = $state->withErrors(['name' => 'Required']);

        self::assertSame(['name' => 'Alice'], $next->values());
        self::assertSame(['name' => 'Required'], $next->errors());
    }

    #[Test]
    public function testConstructionWithInitialValues(): void
    {
        $state = new FormState(
            values: ['name' => 'Bob'],
            errors: ['email' => 'Required'],
            submitted: true,
        );

        self::assertSame(['name' => 'Bob'], $state->values());
        self::assertSame(['email' => 'Required'], $state->errors());
        self::assertTrue($state->isSubmitted());
    }

    #[Test]
    public function testErrorsCanBeMultiple(): void
    {
        $state = (new FormState())->withErrors([
            'email' => ['Invalid format', 'Already in use'],
            'name' => 'Required',
        ]);

        self::assertSame(['Invalid format', 'Already in use'], $state->errors()['email']);
        self::assertSame('Required', $state->errors()['name']);
    }
}

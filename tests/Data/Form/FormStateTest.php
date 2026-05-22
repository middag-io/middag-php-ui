<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.com.br>
 * @copyright   2026 MIDDAG (https://www.middag.com.br)
 * @license     proprietary
 */

namespace Middag\Ui\Tests\Data\Form;

use Middag\Ui\Data\Form\FormState;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(FormState::class)]
final class FormStateTest extends TestCase
{
    #[Test]
    public function testInitialStateIsEmpty(): void
    {
        $state = new FormState();

        self::assertSame([], $state->values());
        self::assertSame([], $state->errors());
        self::assertFalse($state->is_submitted());
    }

    #[Test]
    public function testWithValuesUpdatesValues(): void
    {
        $state = new FormState();
        $next = $state->with_values(['name' => 'Alice', 'email' => 'alice@example.com']);

        self::assertSame(['name' => 'Alice', 'email' => 'alice@example.com'], $next->values());
    }

    #[Test]
    public function testWithValuesMarksSubmitted(): void
    {
        $state = new FormState();
        $next = $state->with_values(['name' => 'Alice']);

        self::assertTrue($next->is_submitted());
    }

    #[Test]
    public function testWithErrorsUpdatesErrors(): void
    {
        $state = new FormState();
        $next = $state->with_errors(['email' => 'Invalid email']);

        self::assertSame(['email' => 'Invalid email'], $next->errors());
    }

    #[Test]
    public function testWithValuesIsImmutable(): void
    {
        $original = new FormState();
        $original->with_values(['name' => 'Alice']);

        self::assertSame([], $original->values());
        self::assertFalse($original->is_submitted());
    }

    #[Test]
    public function testWithErrorsIsImmutable(): void
    {
        $original = new FormState();
        $original->with_errors(['email' => 'Invalid']);

        self::assertSame([], $original->errors());
    }

    #[Test]
    public function testWithValuesDoesNotClearExistingErrors(): void
    {
        $state = (new FormState())->with_errors(['email' => 'Invalid']);
        $next = $state->with_values(['name' => 'Alice']);

        self::assertSame(['email' => 'Invalid'], $next->errors());
        self::assertSame(['name' => 'Alice'], $next->values());
    }

    #[Test]
    public function testWithErrorsDoesNotClearValues(): void
    {
        $state = (new FormState())->with_values(['name' => 'Alice']);
        $next = $state->with_errors(['name' => 'Required']);

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
        self::assertTrue($state->is_submitted());
    }

    #[Test]
    public function testErrorsCanBeMultiple(): void
    {
        $state = (new FormState())->with_errors([
            'email' => ['Invalid format', 'Already in use'],
            'name' => 'Required',
        ]);

        self::assertSame(['Invalid format', 'Already in use'], $state->errors()['email']);
        self::assertSame('Required', $state->errors()['name']);
    }
}

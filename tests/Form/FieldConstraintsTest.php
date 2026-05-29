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

use Middag\Ui\Form\FieldConstraints;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @internal
 */
#[CoversClass(FieldConstraints::class)]
final class FieldConstraintsTest extends TestCase
{
    #[Test]
    public function testIsReadonlyClass(): void
    {
        self::assertTrue((new ReflectionClass(FieldConstraints::class))->isReadOnly());
    }

    #[Test]
    public function testEmptyByDefault(): void
    {
        self::assertSame([], (new FieldConstraints())->jsonSerialize());
    }

    #[Test]
    public function testSerializesAllConstraints(): void
    {
        $payload = (new FieldConstraints(
            required: true,
            min: 1,
            max: 10,
            minLength: 2,
            maxLength: 50,
            pattern: '^[a-z]+$',
            step: '0.5',
        ))->jsonSerialize();

        self::assertSame([
            'required' => true,
            'min' => 1,
            'max' => 10,
            'minLength' => 2,
            'maxLength' => 50,
            'pattern' => '^[a-z]+$',
            'step' => '0.5',
        ], $payload);
    }

    #[Test]
    public function testOmitsRequiredWhenFalse(): void
    {
        $payload = (new FieldConstraints(required: false, min: 5))->jsonSerialize();

        self::assertArrayNotHasKey('required', $payload);
        self::assertSame(5, $payload['min']);
    }
}

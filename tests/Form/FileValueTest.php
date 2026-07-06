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

use Middag\Ui\Form\FileValue;
use Middag\Ui\Tests\Support\ValidatesAgainstSchema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @internal
 */
#[CoversClass(FileValue::class)]
final class FileValueTest extends TestCase
{
    use ValidatesAgainstSchema;

    #[Test]
    public function testIsReadonlyClass(): void
    {
        self::assertTrue((new ReflectionClass(FileValue::class))->isReadOnly());
    }

    #[Test]
    public function testSerializeEmitsOnlyTheUrlWhenMetadataIsNull(): void
    {
        $payload = (new FileValue('https://cdn.example/f.pdf'))->jsonSerialize();

        self::assertSame(['url' => 'https://cdn.example/f.pdf'], $payload);
    }

    #[Test]
    public function testSerializeIncludesEveryMetadataFieldWhenSet(): void
    {
        $payload = (new FileValue(
            url: 'https://cdn.example/f.pdf',
            name: 'report.pdf',
            size: 2048,
            type: 'application/pdf',
            id: 'file-7',
            draftitemid: 99,
        ))->jsonSerialize();

        self::assertSame([
            'url' => 'https://cdn.example/f.pdf',
            'name' => 'report.pdf',
            'size' => 2048,
            'type' => 'application/pdf',
            'id' => 'file-7',
            'draftitemid' => 99,
        ], $payload);
    }

    #[Test]
    public function testSerializedValueValidatesAgainstItsSchema(): void
    {
        $this->assertValidAgainst('FileValue', new FileValue(
            url: 'https://cdn.example/f.pdf',
            name: 'report.pdf',
            size: 2048,
        ));
    }

    #[Test]
    public function testSchemaAcceptsTheBareUrlStringShorthand(): void
    {
        $this->assertValidAgainst('FileValue', 'https://cdn.example/f.pdf');
    }

    #[Test]
    public function testSchemaRejectsAnObjectMissingTheUrl(): void
    {
        $this->assertInvalidAgainst('FileValue', ['name' => 'report.pdf']);
    }

    #[Test]
    public function testSchemaRejectsAnUnknownProperty(): void
    {
        $this->assertInvalidAgainst('FileValue', ['url' => 'https://cdn.example/f.pdf', 'evil' => 1]);
    }
}

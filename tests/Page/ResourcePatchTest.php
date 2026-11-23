<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Tests\Page;

use Middag\Ui\Page\ResourcePatch;
use Middag\Ui\Tests\Support\ValidatesAgainstSchema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @internal
 */
#[CoversClass(ResourcePatch::class)]
final class ResourcePatchTest extends TestCase
{
    use ValidatesAgainstSchema;

    #[Test]
    public function testIsReadonlyClass(): void
    {
        self::assertTrue((new ReflectionClass(ResourcePatch::class))->isReadOnly());
    }

    #[Test]
    public function testEmptyPatchSerializesToAnEmptyObject(): void
    {
        self::assertSame('{}', json_encode(new ResourcePatch()));
    }

    #[Test]
    public function testSerializesFlags(): void
    {
        $patch = new ResourcePatch(featureFlags: ['beta' => false]);

        self::assertSame('{"featureFlags":{"beta":false}}', json_encode($patch));
    }

    #[Test]
    public function testSchemaAcceptsAnEmptyPatch(): void
    {
        $this->assertValidAgainst('ResourcePatch', new ResourcePatch());
    }

    #[Test]
    public function testSchemaAcceptsAPatchWithFlags(): void
    {
        $this->assertValidAgainst('ResourcePatch', new ResourcePatch(
            featureFlags: ['beta' => false],
        ));
    }

    #[Test]
    public function testSchemaRejectsTheRemovedPreferencesKey(): void
    {
        $this->assertInvalidAgainst('ResourcePatch', ['preferences' => ['theme' => 'dark']]);
    }

    #[Test]
    public function testSchemaRejectsAnEmptyArray(): void
    {
        $this->assertInvalidAgainst('ResourcePatch', []);
    }

    #[Test]
    public function testSchemaRejectsAnUnknownPatchProperty(): void
    {
        $this->assertInvalidAgainst('ResourcePatch', ['unknown' => true]);
    }

    #[Test]
    public function testSchemaRejectsTheRemovedCapabilitiesKey(): void
    {
        $this->assertInvalidAgainst('ResourcePatch', ['capabilities' => ['user:edit' => true]]);
    }
}

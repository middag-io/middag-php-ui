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

use Middag\Ui\Page\Branding;
use Middag\Ui\Page\PageResources;
use Middag\Ui\Tests\Support\ValidatesAgainstSchema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(PageResources::class)]
final class PageResourcesTest extends TestCase
{
    use ValidatesAgainstSchema;

    #[Test]
    public function testSerializesDefaults(): void
    {
        self::assertSame('{"featureFlags":{}}', json_encode(new PageResources()));
    }

    #[Test]
    public function testSerializesCustomValues(): void
    {
        $resources = new PageResources(
            featureFlags: ['dark_mode' => true, 'beta' => false],
            branding: new Branding(appName: 'Helico'),
        );

        $payload = $resources->jsonSerialize();

        self::assertSame(['dark_mode' => true, 'beta' => false], $payload['featureFlags']);
        self::assertSame('Helico', $payload['branding']['appName']);
    }

    #[Test]
    public function testNoLongerCarriesUserCapabilitiesOrPreferences(): void
    {
        $payload = (new PageResources(branding: new Branding(appName: 'Helico')))->jsonSerialize();
        $schema = PageResources::jsonSchema();

        foreach (['user', 'capabilities', 'preferences'] as $removed) {
            self::assertArrayNotHasKey($removed, $payload);
            self::assertArrayNotHasKey($removed, $schema['properties']);
            self::assertNotContains($removed, $schema['required']);
        }
        self::assertSame(['featureFlags'], $schema['required']);
    }

    #[Test]
    public function testFeatureFlagsCamelCase(): void
    {
        $resources = new PageResources(featureFlags: ['new_dashboard' => true]);

        self::assertSame('{"featureFlags":{"new_dashboard":true}}', json_encode($resources));
    }

    #[Test]
    public function testSchemaRejectsEmptyFeatureFlagsAsAnArray(): void
    {
        $this->assertInvalidAgainst('PageResources', ['featureFlags' => []]);
    }

    #[Test]
    public function testSchemaAcceptsDefaultResourcesWithEmptyMaps(): void
    {
        $this->assertValidAgainst('PageResources', new PageResources());
    }

    #[Test]
    public function testSchemaAcceptsResourcesWithPopulatedMaps(): void
    {
        $this->assertValidAgainst('PageResources', new PageResources(
            featureFlags: ['dark_mode' => true, 'beta' => false],
            branding: new Branding(appName: 'Helico'),
        ));
    }

    #[Test]
    public function testSchemaRejectsResourcesMissingFeatureFlags(): void
    {
        $this->assertInvalidAgainst('PageResources', []);
    }

    #[Test]
    public function testSchemaRejectsTheRemovedKeys(): void
    {
        $this->assertInvalidAgainst('PageResources', ['featureFlags' => (object) [], 'capabilities' => []]);
        $this->assertInvalidAgainst('PageResources', ['featureFlags' => (object) [], 'user' => ['id' => '1', 'name' => 'A']]);
        $this->assertInvalidAgainst('PageResources', ['featureFlags' => (object) [], 'preferences' => ['theme' => 'dark', 'locale' => 'en']]);
    }

    #[Test]
    public function testSchemaRejectsAnUnknownResourceProperty(): void
    {
        $payload = (new PageResources())->jsonSerialize();
        $payload['extra'] = true;

        $this->assertInvalidAgainst('PageResources', $payload);
    }
}

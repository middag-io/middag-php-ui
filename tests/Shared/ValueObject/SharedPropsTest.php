<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Tests\Shared\ValueObject;

use Middag\Ui\Form\FormErrors;
use Middag\Ui\Navigation\NavigationNode;
use Middag\Ui\Navigation\NavigationTree;
use Middag\Ui\Shared\Enum\ThemeMode;
use Middag\Ui\Shared\ValueObject\FlashMessages;
use Middag\Ui\Shared\ValueObject\Identity;
use Middag\Ui\Shared\ValueObject\SharedAuth;
use Middag\Ui\Shared\ValueObject\SharedProps;
use Middag\Ui\Shared\ValueObject\SharedTheme;
use Middag\Ui\Tests\Support\ValidatesAgainstSchema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use stdClass;

/**
 * @internal
 */
#[CoversClass(SharedProps::class)]
final class SharedPropsTest extends TestCase
{
    use ValidatesAgainstSchema;

    #[Test]
    public function testIsReadonlyClass(): void
    {
        self::assertTrue((new ReflectionClass(SharedProps::class))->isReadOnly());
    }

    #[Test]
    public function testEmptyRootEmitsOnlyAnEmptyScopeObject(): void
    {
        self::assertSame('{"scope":{}}', json_encode(new SharedProps()));
    }

    #[Test]
    public function testSerializesExactWireJson(): void
    {
        $props = new SharedProps(
            auth: new SharedAuth(new Identity(id: '7', name: 'Ada'), ['a:view'], '/logout'),
            theme: new SharedTheme(ThemeMode::Dark),
            flash: new FlashMessages(success: 'Saved'),
            errors: new FormErrors(['email' => 'Required']),
            csrfToken: 'tok',
            locale: 'pt-BR',
            scope: ['org' => 'x'],
            navigation: new NavigationTree([new NavigationNode(key: 'home', label: 'Home')], 'home'),
        );

        self::assertSame(
            '{"auth":{"id":"7","name":"Ada","capabilities":["a:view"],"logoutUrl":"\/logout"},'
            . '"theme":{"appearance":"dark","strings":{}},"flash":{"success":"Saved"},'
            . '"errors":{"email":"Required"},"csrf_token":"tok","locale":"pt-BR","scope":{"org":"x"},'
            . '"navigation":{"tree":[{"key":"home","label":"Home"}],"activeKey":"home","footer":[]}}',
            json_encode($props),
        );
        $this->assertValidAgainst('SharedProps', $props);
    }

    #[Test]
    public function testEmptyMapsSerializeAsObjects(): void
    {
        $props = new SharedProps(
            theme: new SharedTheme(),
            flash: new FlashMessages(),
            errors: new FormErrors(),
        );

        self::assertSame(
            '{"theme":{"strings":{}},"flash":{},"errors":{},"scope":{}}',
            json_encode($props),
        );
        $this->assertValidAgainst('SharedProps', $props);
    }

    #[Test]
    public function testSchemaAcceptsAnUnknownProductKey(): void
    {
        $this->assertValidAgainst('SharedProps', ['middag.x' => ['anything' => true], 'scope' => new stdClass()]);
        $this->assertValidAgainst('SharedProps', ['middag.licensing' => 'ok']);
    }

    #[Test]
    public function testSchemaStaysStrictInsideTypedSlots(): void
    {
        $this->assertInvalidAgainst('SharedProps', ['navigation' => ['tree' => [], 'activeKey' => '', 'footer' => [], 'extra' => 1]]);
        $this->assertInvalidAgainst('SharedProps', ['flash' => ['toast' => 'x']]);
        $this->assertInvalidAgainst('SharedProps', ['auth' => null]);
        $this->assertInvalidAgainst('SharedProps', ['csrf_token' => 1]);
    }

    #[Test]
    public function testSchemaIsOpenAndHasNoRequiredSlots(): void
    {
        $schema = SharedProps::jsonSchema();

        self::assertTrue($schema['additionalProperties']);
        self::assertArrayNotHasKey('required', $schema);
        self::assertSame(
            ['auth', 'theme', 'flash', 'errors', 'csrf_token', 'locale', 'scope', 'navigation'],
            array_keys($schema['properties']),
        );
    }
}

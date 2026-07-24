<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Tests\EditablePanel;

use Middag\Ui\EditablePanel\EditablePanelDescriptor;
use Middag\Ui\Shared\Enum\HttpMethod;
use Middag\Ui\Tests\Support\ValidatesAgainstSchema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(EditablePanelDescriptor::class)]
final class EditablePanelDescriptorTest extends TestCase
{
    use ValidatesAgainstSchema;

    #[Test]
    public function testSerializesWithDefaults(): void
    {
        $panel = new EditablePanelDescriptor(endpoint: '/api/panel/{id}');

        self::assertSame(
            ['endpoint' => '/api/panel/{id}', 'submitMethod' => 'post', 'width' => 440],
            $panel->jsonSerialize(),
        );
    }

    #[Test]
    public function testSerializesSubmitEndpointAndMethodAndWidth(): void
    {
        $panel = new EditablePanelDescriptor(
            endpoint: '/api/panel/{id}',
            submitEndpoint: '/forms/{id}',
            submitMethod: HttpMethod::Put,
            width: 520,
        );

        self::assertSame(
            [
                'endpoint' => '/api/panel/{id}',
                'submitMethod' => 'put',
                'width' => 520,
                'submitEndpoint' => '/forms/{id}',
            ],
            $panel->jsonSerialize(),
        );
    }

    #[Test]
    public function testSchemaAcceptsADefaultPanel(): void
    {
        $this->assertValidAgainst('EditablePanelDescriptor', new EditablePanelDescriptor(endpoint: '/api/panel/{id}'));
    }

    #[Test]
    public function testSchemaAcceptsAPanelWithSubmitEndpoint(): void
    {
        $this->assertValidAgainst('EditablePanelDescriptor', new EditablePanelDescriptor(
            endpoint: '/api/panel/{id}',
            submitEndpoint: '/forms/{id}',
            submitMethod: HttpMethod::Patch,
            width: 500,
        ));
    }

    #[Test]
    public function testSchemaRejectsAPanelMissingItsEndpoint(): void
    {
        $this->assertInvalidAgainst('EditablePanelDescriptor', ['submitMethod' => 'post', 'width' => 440]);
    }

    #[Test]
    public function testSchemaRejectsANonIntegerWidth(): void
    {
        $this->assertInvalidAgainst(
            'EditablePanelDescriptor',
            ['endpoint' => '/api/panel/{id}', 'submitMethod' => 'post', 'width' => '440'],
        );
    }

    #[Test]
    public function testSchemaRejectsAnUnknownProperty(): void
    {
        $this->assertInvalidAgainst(
            'EditablePanelDescriptor',
            ['endpoint' => '/api/panel/{id}', 'submitMethod' => 'post', 'width' => 440, 'height' => 200],
        );
    }
}

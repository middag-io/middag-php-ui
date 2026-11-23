<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Tests\Action;

use Middag\Ui\Action\ActionResult;
use Middag\Ui\Block\BlockDescriptor;
use Middag\Ui\Form\FieldMessage;
use Middag\Ui\Region\Fragment;
use Middag\Ui\Shared\Enum\NotificationLevel;
use Middag\Ui\Shared\ValueObject\Notification;
use Middag\Ui\Tests\Support\ValidatesAgainstSchema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionParameter;

/**
 * @internal
 */
#[CoversClass(ActionResult::class)]
final class ActionResultTest extends TestCase
{
    use ValidatesAgainstSchema;

    #[Test]
    public function testIsReadonlyClass(): void
    {
        self::assertTrue((new ReflectionClass(ActionResult::class))->isReadOnly());
    }

    #[Test]
    public function testSerializesMinimal(): void
    {
        $payload = (new ActionResult())->jsonSerialize();

        self::assertSame(['success' => true], $payload);
    }

    #[Test]
    public function testSerializesAllFields(): void
    {
        $payload = (new ActionResult(
            success: false,
            notifications: [new Notification(NotificationLevel::Error, 'Failed')],
            redirect: '/back',
            refreshBlocks: ['table'],
            errors: ['name' => 'Required'],
        ))->jsonSerialize();

        self::assertFalse($payload['success']);
        self::assertCount(1, $payload['notifications']);
        self::assertSame('error', $payload['notifications'][0]['level']);
        self::assertSame('/back', $payload['redirect']);
        self::assertSame(['table'], $payload['refreshBlocks']);
        self::assertSame(['name' => 'Required'], $payload['errors']);
    }

    #[Test]
    public function testNormalizesStructuredErrorMessages(): void
    {
        $result = new ActionResult(
            success: false,
            errors: ['email' => new FieldMessage('Invalid email.', 'validation.email', 'validators'), 'name' => ['Required', new FieldMessage('Too short.', 'validation.min', 'validators', ['min' => 2])]],
        );

        $payload = $result->jsonSerialize();

        self::assertSame(['message' => 'Invalid email.', 'key' => 'validation.email', 'domain' => 'validators'], $payload['errors']['email']);
        self::assertSame('Required', $payload['errors']['name'][0]);
        self::assertSame(['min' => 2], $payload['errors']['name'][1]['params']);
        $this->assertValidAgainst('ActionResult', $result);
    }

    #[Test]
    public function testOmitsPushFieldsByDefault(): void
    {
        $payload = (new ActionResult())->jsonSerialize();

        self::assertArrayNotHasKey('fragments', $payload);
        self::assertArrayNotHasKey('resources', $payload);
    }

    #[Test]
    public function testSerializesPushFields(): void
    {
        $payload = (new ActionResult(
            fragments: [Fragment::block(new BlockDescriptor(type: 'metric_card', key: 'rev', data: []))],
        ))->jsonSerialize();

        self::assertCount(1, $payload['fragments']);
        self::assertSame('block', $payload['fragments'][0]['kind']);
        self::assertArrayNotHasKey('resources', $payload);
    }

    #[Test]
    public function testSchemaAcceptsAMinimalResult(): void
    {
        $this->assertValidAgainst('ActionResult', new ActionResult());
    }

    #[Test]
    public function testSchemaAcceptsAResultWithPullFields(): void
    {
        $this->assertValidAgainst('ActionResult', new ActionResult(
            success: false,
            notifications: [new Notification(NotificationLevel::Error, 'Failed')],
            redirect: '/back',
            refreshBlocks: ['table'],
            errors: ['name' => 'Required'],
        ));
    }

    #[Test]
    public function testSchemaAcceptsAResultWithPushFields(): void
    {
        $this->assertValidAgainst('ActionResult', new ActionResult(
            fragments: [Fragment::block(new BlockDescriptor(type: 'metric_card', key: 'rev', data: []))],
        ));
    }

    #[Test]
    public function testSchemaRejectsAResultMissingSuccess(): void
    {
        $this->assertInvalidAgainst('ActionResult', ['redirect' => '/x']);
    }

    #[Test]
    public function testSchemaRejectsANonBooleanSuccess(): void
    {
        $this->assertInvalidAgainst('ActionResult', ['success' => 'yes']);
    }

    #[Test]
    public function testSchemaRejectsAnUnknownProperty(): void
    {
        $this->assertInvalidAgainst('ActionResult', ['success' => true, 'unknown' => 1]);
    }

    #[Test]
    public function testSchemaRejectsTheRemovedResourcesSlot(): void
    {
        $this->assertInvalidAgainst('ActionResult', ['success' => true, 'resources' => (object) []]);
    }

    #[Test]
    public function testHasNoResourcesParameter(): void
    {
        $ctor = (new ReflectionClass(ActionResult::class))->getConstructor();
        self::assertNotNull($ctor);

        $names = array_map(static fn (ReflectionParameter $p): string => $p->getName(), $ctor->getParameters());

        self::assertSame(['success', 'notifications', 'redirect', 'refreshBlocks', 'errors', 'fragments'], $names);
    }
}

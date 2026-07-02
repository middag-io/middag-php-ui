<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Region;

use JsonSerializable;
use Middag\Ui\Block\Contract\BlockDescriptorInterface;
use Middag\Ui\Envelope\Contract\ContractEnvelopeInterface;
use Middag\Ui\Page\ResourcePatch;
use Middag\Ui\Shared\Enum\FragmentKind;
use Middag\Ui\Shared\ValueObject\Notification;
use Middag\Ui\Table\TableConfig;

/**
 * A partial, self-describing slice of the contract.
 *
 * The counterpart to the full PageContract: when the client owns the page
 * layout, the server returns a Fragment carrying one ready piece (a block, a
 * table, a region update, notifications, ...) plus its routing kind. The
 * payload reuses the existing value objects — no new data shapes here.
 *
 * Built via the named constructors. A pure-notifications fragment carries no
 * payload; its content lives in the notifications list.
 *
 * @api
 */
final readonly class Fragment implements ContractEnvelopeInterface
{
    /**
     * @param Notification[] $notifications
     */
    private function __construct(
        public FragmentKind $kind,
        public ?JsonSerializable $payload = null,
        public array $notifications = [],
        public ?ResourcePatch $resources = null,
        public ?string $customType = null,
    ) {}

    public static function block(BlockDescriptorInterface $block): self
    {
        return new self(kind: FragmentKind::BLOCK, payload: $block);
    }

    public static function region(RegionUpdate $region): self
    {
        return new self(kind: FragmentKind::REGION, payload: $region);
    }

    public static function table(TableConfig $table): self
    {
        return new self(kind: FragmentKind::TABLE, payload: $table);
    }

    public static function form(BlockDescriptorInterface $form): self
    {
        return new self(kind: FragmentKind::FORM, payload: $form);
    }

    /**
     * @param Notification[] $notifications
     */
    public static function notifications(array $notifications): self
    {
        return new self(kind: FragmentKind::NOTIFICATIONS, notifications: $notifications);
    }

    public static function custom(string $customType, JsonSerializable $payload): self
    {
        return new self(kind: FragmentKind::CUSTOM, payload: $payload, customType: $customType);
    }

    public static function of(FragmentKind $kind, JsonSerializable $payload): self
    {
        return new self(kind: $kind, payload: $payload);
    }

    public function withNotifications(Notification ...$notifications): self
    {
        return new self($this->kind, $this->payload, $notifications, $this->resources, $this->customType);
    }

    public function withResources(ResourcePatch $resources): self
    {
        return new self($this->kind, $this->payload, $this->notifications, $resources, $this->customType);
    }

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        $payload = [
            'version' => self::VERSION,
            'kind' => $this->kind->value,
        ];

        if ($this->payload instanceof JsonSerializable) {
            $payload['payload'] = $this->payload->jsonSerialize();
        }

        if ($this->notifications !== []) {
            $payload['notifications'] = array_map(
                static fn (Notification $notification): array => $notification->jsonSerialize(),
                $this->notifications,
            );
        }

        if ($this->resources instanceof ResourcePatch) {
            $payload['resources'] = $this->resources->jsonSerialize();
        }

        if ($this->kind === FragmentKind::CUSTOM && $this->customType !== null) {
            $payload['customType'] = $this->customType;
        }

        return $payload;
    }

    /** @return array<string, mixed> */
    public static function jsonSchema(): array
    {
        return ['type' => 'object', 'required' => ['version', 'kind'],
            'properties' => ['version' => ['const' => '1'], 'kind' => ['$ref' => '#/$defs/FragmentKind'],
                'payload' => ['type' => 'object', 'additionalProperties' => true],
                'notifications' => ['type' => 'array', 'items' => ['$ref' => '#/$defs/Notification']],
                'resources' => ['$ref' => '#/$defs/ResourcePatch'], 'customType' => ['type' => 'string']],
            'additionalProperties' => false];
    }
}

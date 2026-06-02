<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Action;

use Middag\Ui\Shared\Data\Label;
use Middag\Ui\Shared\Data\Translatable;
use Middag\Ui\Shared\Enum\ActionIntent;

/**
 * The single, canonical action value object.
 *
 * One labeled, intent-styled control with a target ({@see ActionTarget}).
 * Used everywhere an action appears — page header, row, bulk, block — so there
 * is exactly one action shape on the wire. "Bulk" / "row" is positional
 * context, not a distinct type.
 *
 * @api
 */
final readonly class Action implements ActionInterface
{
    /**
     * @param ?string $capability Opaque authorization token, mapped by the host
     *                            adapter (e.g. Moodle capability, WP capability).
     *                            It is NOT a host API call — the framework treats
     *                            it as a transparent string and forwards it on the
     *                            wire; the adapter alone interprets/enforces it.
     */
    public function __construct(
        public string $id,
        public string|Translatable $label,
        public ActionTarget $target,
        public ActionIntent $intent = ActionIntent::SECONDARY,
        public ?string $icon = null,
        public ?Confirmation $confirmation = null,
        public ?string $capability = null,
        public bool $disabled = false,
        public bool $loading = false,
    ) {}

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        $payload = [
            'id' => $this->id,
            'label' => Label::serialize($this->label),
            'target' => $this->target->jsonSerialize(),
            'intent' => $this->intent->value,
        ];

        if ($this->icon !== null) {
            $payload['icon'] = $this->icon;
        }

        if ($this->confirmation instanceof Confirmation) {
            $payload['confirmation'] = $this->confirmation->jsonSerialize();
        }

        if ($this->capability !== null) {
            $payload['capability'] = $this->capability;
        }

        if ($this->disabled) {
            $payload['disabled'] = true;
        }

        if ($this->loading) {
            $payload['loading'] = true;
        }

        return $payload;
    }

    /** @return array<string, mixed> */
    public static function jsonSchema(): array
    {
        return ['type' => 'object', 'required' => ['id', 'label', 'target', 'intent'],
            'properties' => ['id' => ['type' => 'string'], 'label' => ['$ref' => '#/$defs/Label'], 'target' => ['$ref' => '#/$defs/ActionTarget'], 'intent' => ['$ref' => '#/$defs/ActionIntent'], 'icon' => ['type' => 'string'], 'confirmation' => ['$ref' => '#/$defs/Confirmation'], 'capability' => ['type' => 'string'], 'disabled' => ['const' => true], 'loading' => ['const' => true]],
            'additionalProperties' => false];
    }
}

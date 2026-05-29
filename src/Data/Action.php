<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Data;

use Middag\Ui\Contract\ActionInterface;
use Middag\Ui\Enum\ActionIntent;

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
}

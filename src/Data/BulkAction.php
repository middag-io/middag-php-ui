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

use JsonSerializable;
use Middag\Ui\Support\Label;

/**
 * Bulk action applied to selected table rows.
 *
 * @api
 */
final readonly class BulkAction implements JsonSerializable
{
    public function __construct(
        public string $id,
        public string|Translatable $label,
        public string $intent,
        public string $endpoint,
        public string $method = 'post',
        public ?Confirmation $confirmation = null,
        public ?string $capability = null,
    ) {}

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        $payload = [
            'id' => $this->id,
            'label' => Label::serialize($this->label),
            'intent' => $this->intent,
            'endpoint' => $this->endpoint,
            'method' => $this->method,
        ];

        if ($this->confirmation instanceof Confirmation) {
            $payload['confirmation'] = $this->confirmation->jsonSerialize();
        }

        if ($this->capability !== null) {
            $payload['capability'] = $this->capability;
        }

        return $payload;
    }
}

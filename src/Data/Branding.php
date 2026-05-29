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

/**
 * White-label branding for the page shell.
 *
 * `appName` is raw data, not a label. Color/theme live in
 * {@see UserPreferences} and the client.
 *
 * @api
 */
final readonly class Branding implements JsonSerializable
{
    public function __construct(
        public string $appName,
        public ?string $logoUrl = null,
        public ?string $logoCompactUrl = null,
    ) {}

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        $payload = [
            'appName' => $this->appName,
        ];

        if ($this->logoUrl !== null) {
            $payload['logoUrl'] = $this->logoUrl;
        }

        if ($this->logoCompactUrl !== null) {
            $payload['logoCompactUrl'] = $this->logoCompactUrl;
        }

        return $payload;
    }
}

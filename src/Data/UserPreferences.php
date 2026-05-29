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
use Middag\Ui\Enum\ThemeMode;

/**
 * Persisted, server-driven user preferences applied by the client.
 *
 * Carries the values the client needs to resolve i18n and formatting intent
 * (locale, timezone, formats) plus the theme value. The library stores; the
 * client applies. Extensible via the open `extra` bag for host-specific prefs.
 *
 * @api
 */
final readonly class UserPreferences implements JsonSerializable
{
    /**
     * @param array<string, mixed> $extra Open bag for future/host-specific preferences
     */
    public function __construct(
        public ThemeMode $theme = ThemeMode::SYSTEM,
        public string $locale = 'en',
        public ?string $timezone = null,
        public ?string $dateFormat = null,
        public ?string $numberFormat = null,
        public array $extra = [],
    ) {}

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        $typed = [
            'theme' => $this->theme->value,
            'locale' => $this->locale,
        ];

        if ($this->timezone !== null) {
            $typed['timezone'] = $this->timezone;
        }

        if ($this->dateFormat !== null) {
            $typed['dateFormat'] = $this->dateFormat;
        }

        if ($this->numberFormat !== null) {
            $typed['numberFormat'] = $this->numberFormat;
        }

        // Typed keys take precedence over the open bag.
        return array_merge($this->extra, $typed);
    }
}

<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.com.br>
 * @copyright   2026 MIDDAG (https://www.middag.com.br)
 * @license     proprietary
 */

namespace Middag\Ui\Data;

use Middag\Ui\Contract\PageResourcesInterface;

readonly class PageResources implements PageResourcesInterface
{
    /**
     * @param array<string, mixed> $auth
     * @param array<string, bool>  $capabilities
     * @param array<string, bool>  $feature_flags
     */
    public function __construct(
        public array $auth = [],
        public array $capabilities = [],
        public array $feature_flags = [],
        public string $locale = 'pt-BR',
    ) {}

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        return [
            'auth' => $this->auth,
            'capabilities' => $this->capabilities,
            'featureFlags' => $this->feature_flags,
            'locale' => $this->locale,
        ];
    }
}

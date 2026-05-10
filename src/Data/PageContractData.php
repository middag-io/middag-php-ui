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

use Middag\Ui\Contract\PageContractInterface;

readonly class PageContractData implements PageContractInterface
{
    public const VERSION = '1';

    public function __construct(
        public string          $shell,
        public PageMeta        $page,
        public LayoutDescriptor $layout,
        public ?PageResources  $resources = null,
    ) {}

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        $payload = [
            'version' => self::VERSION,
            'shell'   => $this->shell,
            'page'    => $this->page->jsonSerialize(),
            'layout'  => $this->layout->jsonSerialize(),
        ];

        if ($this->resources !== null) {
            $payload['resources'] = $this->resources->jsonSerialize();
        }

        return $payload;
    }
}

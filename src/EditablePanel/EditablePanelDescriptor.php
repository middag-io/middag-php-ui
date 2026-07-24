<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\EditablePanel;

use Middag\Ui\EditablePanel\Contract\EditablePanelDescriptorInterface;
use Middag\Ui\Shared\Enum\HttpMethod;

/**
 * Editable-panel side-drawer descriptor: the GET `endpoint` (with `{id}`
 * placeholder) that returns the form to edit, the save endpoint + method, and
 * the panel width. Delivered as a page-level shared prop (like the inspector);
 * a row action with `ActionTarget::panel()` opens it for the acting row.
 *
 * @api
 */
readonly class EditablePanelDescriptor implements EditablePanelDescriptorInterface
{
    public function __construct(
        public string $endpoint,
        public ?string $submitEndpoint = null,
        public HttpMethod $submitMethod = HttpMethod::Post,
        public int $width = 440,
    ) {}

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        $payload = [
            'endpoint' => $this->endpoint,
            'submitMethod' => $this->submitMethod->value,
            'width' => $this->width,
        ];

        if ($this->submitEndpoint !== null) {
            $payload['submitEndpoint'] = $this->submitEndpoint;
        }

        return $payload;
    }

    /** @return array<string, mixed> */
    public static function jsonSchema(): array
    {
        return [
            'type' => 'object',
            'required' => ['endpoint', 'submitMethod', 'width'],
            'properties' => [
                'endpoint' => ['type' => 'string'],
                'submitEndpoint' => ['type' => 'string'],
                'submitMethod' => ['enum' => ['post', 'put', 'patch']],
                'width' => ['type' => 'integer'],
            ],
            'additionalProperties' => false,
        ];
    }
}
